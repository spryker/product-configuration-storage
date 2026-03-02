<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Client\ProductConfigurationStorage\ProductConfigurationStorageClient;

use Codeception\Test\Unit;
use Generated\Shared\DataBuilder\ProductConfigurationInstanceBuilder;
use Generated\Shared\Transfer\ProductViewTransfer;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Client
 * @group ProductConfigurationStorage
 * @group ProductConfigurationStorageClient
 * @group IsProductConcreteAvailableTest
 * Add your own group annotations below this line
 */
class IsProductConcreteAvailableTest extends Unit
{
    /**
     * @var \SprykerTest\Client\ProductConfigurationStorage\ProductConfigurationStorageClientTester
     */
    protected $tester;

    public function testIsProductConcreteAvailableWithAcceptableAvailableQuantity(): void
    {
        // Arrange
        $productConcreteTransfer = $this->tester->haveProduct();
        $productConfigurationInstanceTransfer = (new ProductConfigurationInstanceBuilder())->build()
            ->setAvailableQuantity(1);

        $this->tester->getClient()->storeProductConfigurationInstanceBySku($productConcreteTransfer->getSku(), $productConfigurationInstanceTransfer);

        $productViewTransfer = (new ProductViewTransfer())
            ->setIdProductConcrete($productConcreteTransfer->getIdProductConcrete())
            ->setSku($productConcreteTransfer->getSku());

        // Act
        $isProductConcreteAvailable = $this->tester
            ->getClient()
            ->isProductConcreteAvailable($productViewTransfer);

        // Assert
        $this->assertTrue(
            $isProductConcreteAvailable,
            'Expects that product concrete will be available.',
        );
    }

    public function testIsProductConcreteAvailableWithNotAcceptableAvailableQuantity(): void
    {
        // Arrange
        $productConcreteTransfer = $this->tester->haveProduct();
        $productConfigurationInstanceTransfer = (new ProductConfigurationInstanceBuilder())->build()
            ->setAvailableQuantity(0);

        $this->tester->getClient()->storeProductConfigurationInstanceBySku($productConcreteTransfer->getSku(), $productConfigurationInstanceTransfer);

        $productViewTransfer = (new ProductViewTransfer())
            ->setIdProductConcrete($productConcreteTransfer->getIdProductConcrete())
            ->setSku($productConcreteTransfer->getSku());

        // Act
        $isProductConcreteAvailable = $this->tester
            ->getClient()
            ->isProductConcreteAvailable($productViewTransfer);

        // Assert
        $this->assertFalse(
            $isProductConcreteAvailable,
            'Expects that product concrete wont be available.',
        );
    }

    public function testIsProductConcreteAvailableWithoutAvailableQuantity(): void
    {
        // Arrange
        $productConcreteTransfer = $this->tester->haveProduct();
        $productConfigurationInstanceTransfer = (new ProductConfigurationInstanceBuilder())->build()
            ->setAvailableQuantity(null);

        $this->tester->getClient()->storeProductConfigurationInstanceBySku($productConcreteTransfer->getSku(), $productConfigurationInstanceTransfer);

        $productViewTransfer = (new ProductViewTransfer())
            ->setIdProductConcrete($productConcreteTransfer->getIdProductConcrete())
            ->setSku($productConcreteTransfer->getSku());

        // Act
        $isProductConcreteAvailable = $this->tester
            ->getClient()
            ->isProductConcreteAvailable($productViewTransfer);

        // Assert
        $this->assertFalse(
            $isProductConcreteAvailable,
            'Expects that product concrete wont be available when available quantity of product configuration is null.',
        );
    }

    public function testIsProductConcreteAvailableWithoutInstanceWithFalseAvailableInProductView(): void
    {
        // Arrange
        $this->tester->setupStorageRedisConfig();
        $productConcreteTransfer = $this->tester->haveProduct();

        $productViewTransfer = (new ProductViewTransfer())
            ->setIdProductConcrete($productConcreteTransfer->getIdProductConcrete())
            ->setSku($productConcreteTransfer->getSku())
            ->setAvailable(false);

        // Act
        $isProductConcreteAvailable = $this->tester
            ->getClient()
            ->isProductConcreteAvailable($productViewTransfer);

        // Assert
        $this->assertFalse(
            $isProductConcreteAvailable,
            'Expects that product concrete wont be available when no product configuration and no availability in product view.',
        );
    }

    public function testIsProductConcreteAvailableWithoutInstanceWithTrueAvailableInProductView(): void
    {
        // Arrange
        $this->tester->setupStorageRedisConfig();
        $productConcreteTransfer = $this->tester->haveProduct();

        $productViewTransfer = (new ProductViewTransfer())
            ->setIdProductConcrete($productConcreteTransfer->getIdProductConcrete())
            ->setSku($productConcreteTransfer->getSku())
            ->setAvailable(true);

        // Act
        $isProductConcreteAvailable = $this->tester
            ->getClient()
            ->isProductConcreteAvailable($productViewTransfer);

        // Assert
        $this->assertTrue(
            $isProductConcreteAvailable,
            'Expects that product concrete will be available when no product configuration but available in product view.',
        );
    }
}
