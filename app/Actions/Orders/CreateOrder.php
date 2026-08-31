<?php

namespace App\Actions\Orders;

use App\Enums\AddressType;
use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CreateOrder
{
    /**
     * Create an order with customer, items, and addresses in a single atomic transaction
     */
    public function execute(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // 1. Resolve or create customer (Lookup by phone to avoid duplicates)
            if (!empty($data['customer_id'])) {
                $customer = Customer::findOrFail($data['customer_id']);
            } elseif (!empty($data['customer_phone'])) {
                $customer = Customer::firstOrCreate(
                    ['phone' => $data['customer_phone']],
                    [
                        'name' => $data['customer_name'] ?? 'Pelanggan',
                        'email' => $data['customer_email'] ?? null,
                        'notes' => $data['customer_notes'] ?? null,
                    ]
                );

                if (!empty($data['customer_name']) && $customer->name === 'Pelanggan') {
                    $customer->update([
                        'name' => $data['customer_name'],
                        'email' => $data['customer_email'] ?? $customer->email,
                    ]);
                }
            } else {
                $customer = Customer::create([
                    'name' => $data['customer_name'] ?? 'Pelanggan',
                    'phone' => $data['customer_phone'] ?? '-',
                    'email' => $data['customer_email'] ?? null,
                    'notes' => $data['customer_notes'] ?? null,
                ]);
            }

            // 2. Create the Order
            $order = Order::create([
                'customer_id' => $customer->id,
                'service_id' => $data['service_id'],
                'status' => $data['status'] ?? OrderStatus::PENDING_REVIEW,
                'preferred_date' => $data['preferred_date'] ?? null,
                'customer_notes' => $data['customer_notes'] ?? null,
                'admin_notes' => $data['admin_notes'] ?? null,
                'total_amount' => $data['total_amount'] ?? 0,
            ]);


            // 3. Create items
            if (!empty($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $item) {
                    if (!empty($item['name'])) {
                        $order->items()->create([
                            'name' => $item['name'],
                            'description' => $item['description'] ?? null,
                            'quantity' => $item['quantity'] ?? 1,
                            'estimated_size' => $item['estimated_size'] ?? null,
                            'notes' => $item['notes'] ?? null,
                        ]);
                    }
                }
            }

            // 4. Create Pickup Address
            if (!empty($data['pickup_address'])) {
                $pickup = is_array($data['pickup_address']) ? $data['pickup_address'] : ['address' => $data['pickup_address']];
                $order->addresses()->create([
                    'type' => AddressType::PICKUP,
                    'address' => $pickup['address'] ?? '-',
                    'city' => $pickup['city'] ?? 'Kota Malang',
                    'district' => $pickup['district'] ?? null,
                    'latitude' => $pickup['latitude'] ?? null,
                    'longitude' => $pickup['longitude'] ?? null,
                    'notes' => $pickup['notes'] ?? null,
                ]);
            }

            // 5. Create Destination Address (if provided)
            if (!empty($data['destination_address'])) {
                $dest = is_array($data['destination_address']) ? $data['destination_address'] : ['address' => $data['destination_address']];
                $order->addresses()->create([
                    'type' => AddressType::DESTINATION,
                    'address' => $dest['address'] ?? '-',
                    'city' => $dest['city'] ?? 'Kota Malang',
                    'district' => $dest['district'] ?? null,
                    'latitude' => $dest['latitude'] ?? null,
                    'longitude' => $dest['longitude'] ?? null,
                    'notes' => $dest['notes'] ?? null,
                ]);
            }

            return $order->fresh(['customer', 'service', 'items', 'addresses', 'statusHistories']);
        });
    }
}
