<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryPhoto;
use App\Models\OrderAttachment;
use App\Models\Payment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SecureFileController extends Controller
{
    /**
     * Securely deliver payment proof to authorized staff.
     */
    public function showPaymentProof(Payment $payment): BinaryFileResponse|StreamedResponse
    {
        Gate::authorize('manage-payments');

        $path = $payment->proof_path;
        if (! $path) {
            abort(404, 'Bukti pembayaran tidak ditemukan.');
        }

        if (! Storage::disk('local')->exists($path)) {
            abort(404, 'File bukti pembayaran tidak ditemukan pada disk penyimpanan.');
        }

        return Storage::disk('local')->response($path, basename($path), [
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Securely deliver inventory photo to authorized staff.
     */
    public function showInventoryPhoto(InventoryPhoto $inventoryPhoto): BinaryFileResponse|StreamedResponse
    {
        Gate::authorize('manage-inventory');

        $path = $inventoryPhoto->file_path;
        if (! $path) {
            abort(404, 'Foto dokumentasi tidak ditemukan.');
        }

        if (! Storage::disk('local')->exists($path)) {
            abort(404, 'File foto dokumentasi tidak ditemukan pada disk penyimpanan.');
        }

        return Storage::disk('local')->response($path, $inventoryPhoto->file_name ?: basename($path), [
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Securely deliver order attachment to authorized staff.
     */
    public function showOrderAttachment(OrderAttachment $attachment): BinaryFileResponse|StreamedResponse
    {
        Gate::authorize('manage-orders');

        $path = $attachment->file_path;
        if (! $path) {
            abort(404, 'Lampiran pesanan tidak ditemukan.');
        }

        if (! Storage::disk('local')->exists($path)) {
            abort(404, 'File lampiran tidak ditemukan pada disk penyimpanan.');
        }

        return Storage::disk('local')->response($path, $attachment->original_name ?: basename($path), [
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
