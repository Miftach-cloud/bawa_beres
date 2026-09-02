<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Livewire\Admin\Payments\Index as PaymentIndex;
use App\Livewire\Admin\Payments\Manager as PaymentManager;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaymentSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $operation;

    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->operation = User::factory()->operation()->create();
        $this->order = Order::factory()->create([
            'status' => OrderStatus::CONFIRMED,
            'total_amount' => 400000,
        ]);
    }

    #[Test]
    public function admin_can_view_payments_index_and_filter(): void
    {
        $payment1 = Payment::create([
            'order_id' => $this->order->id,
            'payment_number' => Payment::generateNumber($this->order),
            'method' => PaymentMethod::BANK_TRANSFER,
            'status' => PaymentStatus::WAITING_VERIFICATION,
            'amount' => 200000,
        ]);

        $payment2 = Payment::create([
            'order_id' => $this->order->id,
            'payment_number' => Payment::generateNumber($this->order),
            'method' => PaymentMethod::QRIS,
            'status' => PaymentStatus::PAID,
            'amount' => 200000,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(PaymentIndex::class)
            ->assertSee($payment1->payment_number)
            ->assertSee($payment2->payment_number)
            ->set('statusFilter', PaymentStatus::WAITING_VERIFICATION->value)
            ->assertSee($payment1->payment_number)
            ->assertDontSee($payment2->payment_number);
    }

    #[Test]
    public function admin_can_record_payment_with_proof_via_manager(): void
    {
        Storage::fake('local');
        $this->actingAs($this->admin);

        $file = UploadedFile::fake()->image('bukti_tf.png');

        Livewire::test(PaymentManager::class, ['order' => $this->order])
            ->call('openRecordModal')
            ->set('method', PaymentMethod::BANK_TRANSFER->value)
            ->set('amount', 250000)
            ->set('bankName', 'BCA')
            ->set('accountName', 'Budi Santoso')
            ->set('proofFile', $file)
            ->call('savePayment')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('payments', [
            'order_id' => $this->order->id,
            'method' => PaymentMethod::BANK_TRANSFER->value,
            'status' => PaymentStatus::WAITING_VERIFICATION->value,
            'amount' => 250000,
            'bank_name' => 'BCA',
        ]);
    }

    #[Test]
    public function verifying_final_payment_advances_order_to_paid(): void
    {
        $this->actingAs($this->admin);

        // Record full payment
        $payment = Payment::create([
            'order_id' => $this->order->id,
            'payment_number' => Payment::generateNumber($this->order),
            'method' => PaymentMethod::BANK_TRANSFER,
            'status' => PaymentStatus::WAITING_VERIFICATION,
            'amount' => 400000,
        ]);

        Livewire::test(PaymentManager::class, ['order' => $this->order])
            ->call('verify', $payment->id);

        $payment->refresh();
        $this->order->refresh();

        $this->assertEquals(PaymentStatus::PAID, $payment->status);
        $this->assertEquals($this->admin->id, $payment->verified_by);
        $this->assertEquals(OrderStatus::PAID, $this->order->status);
    }

    #[Test]
    public function admin_can_reject_payment_with_reason(): void
    {
        $this->actingAs($this->admin);

        $payment = Payment::create([
            'order_id' => $this->order->id,
            'payment_number' => Payment::generateNumber($this->order),
            'method' => PaymentMethod::BANK_TRANSFER,
            'status' => PaymentStatus::WAITING_VERIFICATION,
            'amount' => 100000,
        ]);

        Livewire::test(PaymentManager::class, ['order' => $this->order])
            ->call('openRejectModal', $payment->id)
            ->set('rejectionReason', 'Nominal transfer tidak sesuai invoice')
            ->call('confirmReject');

        $payment->refresh();
        $this->assertEquals(PaymentStatus::REJECTED, $payment->status);
        $this->assertEquals('Nominal transfer tidak sesuai invoice', $payment->rejection_reason);
    }

    #[Test]
    public function operation_role_cannot_manage_payments(): void
    {
        $this->actingAs($this->operation);

        $this->get('/admin/payments')->assertStatus(403);
    }

    #[Test]
    public function admin_cannot_record_payment_above_the_remaining_balance(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PaymentManager::class, ['order' => $this->order])
            ->call('openRecordModal')
            ->set('amount', 405000)
            ->call('savePayment')
            ->assertHasErrors(['amount']);

        $this->assertDatabaseCount('payments', 0);
    }

    #[Test]
    public function admin_cannot_verify_a_payment_that_would_overpay_the_order(): void
    {
        Payment::create([
            'order_id' => $this->order->id,
            'payment_number' => Payment::generateNumber($this->order),
            'method' => PaymentMethod::CASH,
            'status' => PaymentStatus::PAID,
            'amount' => 300000,
        ]);
        $pendingPayment = Payment::create([
            'order_id' => $this->order->id,
            'payment_number' => Payment::generateNumber($this->order),
            'method' => PaymentMethod::BANK_TRANSFER,
            'status' => PaymentStatus::WAITING_VERIFICATION,
            'amount' => 150000,
        ]);
        $this->actingAs($this->admin);

        Livewire::test(PaymentManager::class, ['order' => $this->order])
            ->call('verify', $pendingPayment->id)
            ->assertHasErrors(['payment']);

        $this->assertSame(PaymentStatus::WAITING_VERIFICATION, $pendingPayment->fresh()->status);
    }

    #[Test]
    public function payment_form_uses_the_configured_default_bank(): void
    {
        Config::set('business.payments.default_bank', 'Bank Operasional');
        $this->actingAs($this->admin);

        Livewire::test(PaymentManager::class, ['order' => $this->order])
            ->call('openRecordModal')
            ->assertSet('bankName', 'Bank Operasional');
    }
}
