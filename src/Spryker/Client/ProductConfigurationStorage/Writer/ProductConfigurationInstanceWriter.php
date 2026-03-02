<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductConfigurationStorage\Writer;

use Generated\Shared\Transfer\ProductConfigurationInstanceTransfer;
use Spryker\Client\ProductConfigurationStorage\Builder\ProductConfigurationSessionKeyBuilderInterface;
use Spryker\Client\ProductConfigurationStorage\Dependency\Client\ProductConfigurationStorageToSessionClientInterface;

class ProductConfigurationInstanceWriter implements ProductConfigurationInstanceWriterInterface
{
    /**
     * @var \Spryker\Client\ProductConfigurationStorage\Dependency\Client\ProductConfigurationStorageToSessionClientInterface
     */
    protected $sessionClient;

    /**
     * @var \Spryker\Client\ProductConfigurationStorage\Builder\ProductConfigurationSessionKeyBuilderInterface
     */
    protected $productConfigurationSessionKeyBuilder;

    public function __construct(
        ProductConfigurationStorageToSessionClientInterface $sessionClient,
        ProductConfigurationSessionKeyBuilderInterface $productConfigurationSessionKeyBuilder
    ) {
        $this->sessionClient = $sessionClient;
        $this->productConfigurationSessionKeyBuilder = $productConfigurationSessionKeyBuilder;
    }

    public function storeProductConfigurationInstanceBySku(
        string $sku,
        ProductConfigurationInstanceTransfer $productConfigurationInstanceTransfer
    ): void {
        $productConfigurationSessionKey = $this->productConfigurationSessionKeyBuilder->getProductConfigurationSessionKey($sku);

        $this->sessionClient->set($productConfigurationSessionKey, $productConfigurationInstanceTransfer);
    }
}
