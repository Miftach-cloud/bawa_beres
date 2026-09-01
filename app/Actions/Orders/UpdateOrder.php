<?php

namespace App\Actions\Orders;

use App\Enums\AddressType;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class UpdateOrder
{
    /**
     * Update order details, notes, and addresses
     */
    public function execute(Order $order, array $data): Order
    {
        return DB::transaction(function () use ($order, $data) {
            $order->update(array_filter([
                'service_id' => $data['service_id'] ?? null,
                'customer_notes' => array_key_exists('customer_notes', $data) ? $data['customer_notes'] : $order->customer_notes,
                'admin_notes' => array_key_exists('admin_notes', $data) ? $data['admin_notes'] : $order->admin_notes,
                'total_amount' => array_key_exists('total_amount', $data) ? $data['total_amount'] : $order->total_amount,
            ], fn ($val) => ! is_null($val)));

            // Update pickup address if provided
            if (isset($data['pickup_address'])) {
                $pickupData = is_array($data['pickup_address']) ? $data['pickup_address'] : ['address' => $data['pickup_address']];
                $order->addresses()->updateOrCreate(
                    ['type' => AddressType::PICKUP],
                    [
                        'address' => $pickupData['address'] ?? '-',
                        'city' => $pickupData['city'] ?? 'Kota Malang',
                        'district' => $pickupData['district'] ?? null,
                        'notes' => $pickupData['notes'] ?? null,
                    ]
                );
            }

            // Update destination address if provided
            if (isset($data['destination_address'])) {
                $destData = is_array($data['destination_address']) ? $data['destination_address'] : ['address' => $data['destination_address']];
                $order->addresses()->updateOrCreate(
                    ['type' => AddressType::DESTINATION],
                    [
                        'address' => $destData['address'] ?? '-',
                        'city' => $destData['city'] ?? 'Kota Malang',
                        'district' => $destData['district'] ?? null,
                        'notes' => $destData['notes'] ?? null,
                    ]
                );
            }

            return $order->fresh(['customer', 'service', 'items', 'addresses']);
        });
    }
}
