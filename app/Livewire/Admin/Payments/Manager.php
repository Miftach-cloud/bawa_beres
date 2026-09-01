<?php

namespace App\Livewire\Admin\Payments;

use App\Actions\Payments\RecordPayment;
use App\Actions\Payments\RejectPayment;
use App\Actions\Payments\VerifyPayment;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Manager extends Component
{
    use WithFileUploads;

    public Order $order;

    // Record Payment Modal
    public bool $showRecordModal = false;
    public string $method = 'BANK_TRANSFER';
    public float|string $amount = 0;
    public string $bankName = 'BCA';
    public string $accountName = '';
    public string $notes = '';
    public $proofFile = null;

    // Verify / Reject Modal
    public bool $showRejectModal = false;
    public ?int $selectedPaymentId = null;
    public string $rejectionReason = '';

    // Proof Preview Modal
    public bool $showProofModal = false;
    public ?string $previewProofUrl = null;

    protected function rules(): array
    {
        return [
            'method' => 'required|string|in:BANK_TRANSFER,QRIS,CASH',
            'amount' => 'required|numeric|min:1000',
            'bankName' => 'nullable|string|max:100',
            'accountName' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:500',
            'proofFile' => 'nullable|image|max:5120', // max 5MB image
        ];
    }

    public function mount(Order $order): void
    {
        $this->order = $order;
        $this->amount = $this->order->remainingBalance() ?: (float) $this->order->total_amount;
    }

    public function openRecordModal(): void
    {
        Gate::authorize('manage-payments');

        $this->method = 'BANK_TRANSFER';
        $this->amount = $this->order->remainingBalance() ?: (float) $this->order->total_amount;
        $this->bankName = 'Bank Central Asia (BCA)';
        $this->accountName = $this->order->customer->name;
        $this->notes = '';
        $this->proofFile = null;
        $this->resetValidation();
        $this->showRecordModal = true;
    }

    public function closeRecordModal(): void
    {
        $this->showRecordModal = false;
        $this->proofFile = null;
        $this->resetValidation();
    }

    public function savePayment(RecordPayment $recordPayment): void
    {
        Gate::authorize('manage-payments');

        $this->validate();

        $payment = $recordPayment->execute($this->order, [
            'method' => $this->method,
            'amount' => (float) $this->amount,
            'bank_name' => $this->bankName ?: null,
            'account_name' => $this->accountName ?: null,
            'notes' => $this->notes ?: null,
            'status' => $this->method === 'CASH' ? PaymentStatus::PAID : PaymentStatus::WAITING_VERIFICATION,
        ], $this->proofFile);

        $this->showRecordModal = false;
        $this->proofFile = null;
        $this->order->refresh();

        session()->flash('payment_message', "Pembayaran #{$payment->payment_number} sebesar Rp " . number_format($payment->amount, 0, ',', '.') . " berhasil dicatat.");
    }

    public function verify(int $paymentId, VerifyPayment $verifyPayment): void
    {
        Gate::authorize('manage-payments');

        $payment = Payment::findOrFail($paymentId);
        $verifyPayment->execute($payment, Auth::user());

        $this->order->refresh();
        session()->flash('payment_message', "Pembayaran #{$payment->payment_number} berhasil diverifikasi (PAID).");
    }

    public function openRejectModal(int $paymentId): void
    {
        $this->selectedPaymentId = $paymentId;
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal = false;
        $this->selectedPaymentId = null;
        $this->rejectionReason = '';
    }

    public function confirmReject(RejectPayment $rejectPayment): void
    {
        Gate::authorize('manage-payments');

        $this->validate([
            'rejectionReason' => 'required|string|min:3|max:500',
        ], [
            'rejectionReason.required' => 'Alasan penolakan bukti pembayaran wajib diisi.',
        ]);

        $payment = Payment::findOrFail($this->selectedPaymentId);
        $rejectPayment->execute($payment, $this->rejectionReason, Auth::user());

        $this->showRejectModal = false;
        $this->order->refresh();
        session()->flash('payment_message', "Pembayaran #{$payment->payment_number} telah ditolak.");
    }

    public function viewProof(string $url): void
    {
        $this->previewProofUrl = $url;
        $this->showProofModal = true;
    }

    public function closeProofModal(): void
    {
        $this->showProofModal = false;
        $this->previewProofUrl = null;
    }

    public function render()
    {
        $payments = $this->order->payments()->with('verifier')->get();

        return view('livewire.admin.payments.manager', [
            'payments' => $payments,
            'totalPaid' => $this->order->totalPaid(),
            'remainingBalance' => $this->order->remainingBalance(),
            'isFullyPaid' => $this->order->isFullyPaid(),
            'methods' => PaymentMethod::cases(),
        ]);
    }
}
