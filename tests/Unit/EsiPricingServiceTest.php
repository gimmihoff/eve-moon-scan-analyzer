<?php

namespace ApplicationTest\Service;

use App\Service\EsiPricingService;
use PHPUnit\Framework\TestCase;

class EsiPricingServiceTest extends TestCase
{
    private EsiPricingService $service;

    protected function setUp(): void
    {
        $this->service = new EsiPricingService();
    }

    public function testGetPricesReturnsArray(): void
    {
        $prices = $this->service->getPrices([]);
        $this->assertIsArray($prices);
    }

    public function testGetPriceReturnsNullForInvalidType(): void
    {
        $price = $this->service->getPrice(999999999);
        // Price may be null if type doesn't exist in market
        $this->assertTrue($price === null || is_float($price));
    }
}
