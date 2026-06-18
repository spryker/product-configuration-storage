<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Client\ProductConfigurationStorage\ProductConfigurationStorageClient;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\ProductConfigurationInstanceBuilder;
use Generated\Shared\Transfer\PriceProductFilterTransfer;
use Generated\Shared\Transfer\PriceProductTransfer;
use Generated\Shared\Transfer\ProductConfigurationInstanceTransfer;
use Generated\Shared\Transfer\ProductViewTransfer;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Client
 * @group ProductConfigurationStorage
 * @group ProductConfigurationStorageClient
 * @group ExpandPriceProductFilterWithProductConfigurationInstanceTest
 * Add your own group annotations below this line
 */
class ExpandPriceProductFilterWithProductConfigurationInstanceTest extends Unit
{
    protected const string PRODUCT_SKU = 'configurable-product-sku';

    /**
     * @var \SprykerTest\Client\ProductConfigurationStorage\ProductConfigurationStorageClientTester
     */
    protected $tester;

    public function testGivenProductViewWithConfigurationInstanceWithPricesWhenExpandedThenFilterGetsSkuAndInstance(): void
    {
        // Arrange
        $productConfigurationInstanceTransfer = (new ProductConfigurationInstanceBuilder([
            ProductConfigurationInstanceTransfer::PRICES => new PriceProductTransfer(),
        ]))->build();
        $productViewTransfer = (new ProductViewTransfer())
            ->setSku(static::PRODUCT_SKU)
            ->setProductConfigurationInstance($productConfigurationInstanceTransfer);

        // Act
        $priceProductFilterTransfer = $this->tester
            ->getClient()
            ->expandPriceProductFilterWithProductConfigurationInstance($productViewTransfer, new PriceProductFilterTransfer());

        // Assert
        $this->assertSame(
            static::PRODUCT_SKU,
            $priceProductFilterTransfer->getSku(),
            'Expects that filter SKU is set to the product view SKU so configuration prices are matched.',
        );
        $this->assertEquals(
            $productConfigurationInstanceTransfer,
            $priceProductFilterTransfer->getProductConfigurationInstance(),
            'Expects that filter will be expanded with product configuration.',
        );
    }

    public function testGivenProductViewWithoutConfigurationInstanceWhenExpandedThenFilterIsUnchanged(): void
    {
        // Arrange

        // Act
        $priceProductFilterTransfer = $this->tester
            ->getClient()
            ->expandPriceProductFilterWithProductConfigurationInstance(new ProductViewTransfer(), new PriceProductFilterTransfer());

        // Assert
        $this->assertNull(
            $priceProductFilterTransfer->getSku(),
            'Expects that filter SKU is not set when there is no product configuration instance.',
        );
        $this->assertNull(
            $priceProductFilterTransfer->getProductConfigurationInstance(),
            'Expects that filter will not be expanded with product configuration when no product configuration.',
        );
    }

    public function testGivenProductViewWithConfigurationInstanceWithoutPricesWhenExpandedThenFilterIsUnchanged(): void
    {
        // Arrange
        $productConfigurationInstanceTransfer = (new ProductConfigurationInstanceBuilder())->build();
        $productConfigurationInstanceTransfer->setPrices(new ArrayObject());
        $productViewTransfer = (new ProductViewTransfer())
            ->setSku(static::PRODUCT_SKU)
            ->setProductConfigurationInstance($productConfigurationInstanceTransfer);

        // Act
        $priceProductFilterTransfer = $this->tester
            ->getClient()
            ->expandPriceProductFilterWithProductConfigurationInstance($productViewTransfer, new PriceProductFilterTransfer());

        // Assert
        $this->assertNull(
            $priceProductFilterTransfer->getSku(),
            'Expects that filter SKU is not set when the configuration instance has no prices.',
        );
        $this->assertNull(
            $priceProductFilterTransfer->getProductConfigurationInstance(),
            'Expects that filter will not be expanded when the configuration instance has no prices.',
        );
    }
}
