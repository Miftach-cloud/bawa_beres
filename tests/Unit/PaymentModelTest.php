<?php

namespace Tests\Unit;

use App\Actions\Payments\RecordPayment;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaymentModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function order_supports_multiple_partial_payments_and_computes_balance(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create([
            'status' => OrderStatus::CONFIRMED,
            'total_amount' => 500000,
        ]);

        $this->assertEquals(0, $order->totalPaid());
        $this->assertEquals(500000, $order->remainingBalance());
        $this->assertFalse($order->isFullyPaid());

        // 1. Partial DP payment (200.000)
        $payment1 = Payment::create([
            'order_id' => $order->id,
            'payment_number' => Payment::generateNumber($order),
            'method' => PaymentMethod::BANK_TRANSFER,
            'status' => PaymentStatus::PAID,
            'amount' => 200000,
            'paid_at' => now(),
            'verified_at' => now(),
            'verified_by' => $admin->id,
        ]);

        $order->refresh();
        $this->assertEquals(200000, $order->totalPaid());
        $this->assertEquals(300000, $order->remainingBalance());
        $this->assertFalse($order->isFullyPaid());

        // 2. Final Settlement payment (300.000)
        $payment2 = Payment::create([
            'order_id' => $order->id,
            'payment_number' => Payment::generateNumber($order),
            'method' => PaymentMethod::CASH,
            'status' => PaymentStatus::PAID,
            'amount' => 300000,
            'paid_at' => now(),
            'verified_at' => now(),
            'verified_by' => $admin->id,
        ]);

        $order->refresh();
        $this->assertEquals(500000, $order->totalPaid());
        $this->assertEquals(0, $order->remainingBalance());
        $this->assertTrue($order->isFullyPaid());
        $this->assertCount(2, $order->payments);
    }

    #[Test]
    public function record_payment_stores_proof_file_and_generates_url(): void
    {
        Storage::fake('local');

        $order = Order::factory()->create();
        $file = UploadedFile::fake()->image('transfer_receipt.jpg');

        $action = app(RecordPayment::class);
        $payment = $action->execute($order, [
            'method' => PaymentMethod::BANK_TRANSFER,
            'amount' => 150000,
            'bank_name' => 'BCA',
            'account_name' => 'Ahmad',
        ], $file);

        $this->assertNotNull($payment->proof_path);
        Storage::disk('local')->assertExists($payment->proof_path);
        $this->assertNotNull($payment->proof_url);
        $this->assertEquals(PaymentStatus::WAITING_VERIFICATION, $payment->status);
    }
}
