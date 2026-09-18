<?php

namespace Tests\Unit;

use App\Enums\AddressType;
use App\Enums\OrderStatus;
use App\Enums\PricingType;
use PHPUnit\Framework\TestCase;

class DomainModelTest extends TestCase
{
    public function test_pricing_type_enum_returns_valid_labels(): void
    {
        $this->assertEquals('Harga Tetap (Fixed)', PricingType::FIXED->label());
        $this->assertEquals('Paket Layanan', PricingType::PACKAGE->label());
        $this->assertEquals('Estimasi / Quotation Admin', PricingType::QUOTATION->label());
    }

    public function test_order_status_enum_helpers(): void
    {
        $this->assertTrue(OrderStatus::COMPLETED->isFinal());
        $this->assertTrue(OrderStatus::CANCELLED->isFinal());
        $this->assertFalse(OrderStatus::PENDING_REVIEW->isFinal());
        $this->assertNotEmpty(OrderStatus::PAID->badgeColor());
    }

    public function test_address_type_enum_labels(): void
    {
        $this->assertEquals('Lokasi Penjemputan (Pickup)', AddressType::PICKUP->label());
        $this->assertEquals('Lokasi Tujuan / Pengantaran', AddressType::DESTINATION->label());
    }
}
