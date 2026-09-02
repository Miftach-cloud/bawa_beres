<?php

namespace App\Listeners;

use App\Enums\UserRole;
use App\Events\InventoryReceived;
use App\Events\InventoryStored;
use App\Events\OrderCompleted;
use App\Events\OrderConfirmed;
use App\Events\OrderCreated;
use App\Events\PickupScheduled;
use App\Events\QuotationCreated;
use App\Models\User;
use App\Notifications\InventoryReceivedNotification;
use App\Notifications\InventoryStoredNotification;
use App\Notifications\OrderCompletedNotification;
use App\Notifications\OrderConfirmedNotification;
use App\Notifications\OrderCreatedNotification;
use App\Notifications\PickupScheduledNotification;
use App\Notifications\QuotationCreatedNotification;
use Illuminate\Support\Facades\Notification;

class SendOrderNotifications
{
    /**
     * Notify internal staff users (Admin, Operation, Owner)
     */
    protected function getInternalStaff()
    {
        return User::whereIn('role', [
            UserRole::OWNER,
            UserRole::ADMIN,
            UserRole::OPERATION,
        ])->get();
    }

    public function handleOrderCreated(OrderCreated $event): void
    {
        $notification = new OrderCreatedNotification($event->order);

        // Notify internal staff
        Notification::send($this->getInternalStaff(), $notification);

        // Notify customer if customer model exists
        if ($event->order->customer) {
            $event->order->customer->notify($notification);
        }
    }

    public function handleQuotationCreated(QuotationCreated $event): void
    {
        $notification = new QuotationCreatedNotification($event->quotation);

        Notification::send($this->getInternalStaff(), $notification);

        if ($event->quotation->order?->customer) {
            $event->quotation->order->customer->notify($notification);
        }
    }

    public function handleOrderConfirmed(OrderConfirmed $event): void
    {
        $notification = new OrderConfirmedNotification($event->order);

        Notification::send($this->getInternalStaff(), $notification);

        if ($event->order->customer) {
            $event->order->customer->notify($notification);
        }
    }

    public function handlePickupScheduled(PickupScheduled $event): void
    {
        $notification = new PickupScheduledNotification($event->order, $event->schedule);

        Notification::send($this->getInternalStaff(), $notification);

        if ($event->order->customer) {
            $event->order->customer->notify($notification);
        }
    }

    public function handleInventoryReceived(InventoryReceived $event): void
    {
        $notification = new InventoryReceivedNotification($event->order, $event->inventoryItem);

        Notification::send($this->getInternalStaff(), $notification);

        if ($event->order->customer) {
            $event->order->customer->notify($notification);
        }
    }

    public function handleInventoryStored(InventoryStored $event): void
    {
        $notification = new InventoryStoredNotification($event->inventoryItem, $event->location);

        Notification::send($this->getInternalStaff(), $notification);

        if ($event->inventoryItem->order?->customer) {
            $event->inventoryItem->order->customer->notify($notification);
        }
    }

    public function handleOrderCompleted(OrderCompleted $event): void
    {
        $notification = new OrderCompletedNotification($event->order);

        Notification::send($this->getInternalStaff(), $notification);

        if ($event->order->customer) {
            $event->order->customer->notify($notification);
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array<string, string>
     */
    public function subscribe(): array
    {
        return [
            OrderCreated::class => 'handleOrderCreated',
            QuotationCreated::class => 'handleQuotationCreated',
            OrderConfirmed::class => 'handleOrderConfirmed',
            PickupScheduled::class => 'handlePickupScheduled',
            InventoryReceived::class => 'handleInventoryReceived',
            InventoryStored::class => 'handleInventoryStored',
            OrderCompleted::class => 'handleOrderCompleted',
        ];
    }
}
