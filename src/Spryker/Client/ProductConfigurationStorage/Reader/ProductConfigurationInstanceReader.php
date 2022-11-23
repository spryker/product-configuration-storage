<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductConfigurationStorage\Reader;

use ArrayObject;
use Generated\Shared\Transfer\ProductConfigurationInstanceCollectionTransfer;
use Generated\Shared\Transfer\ProductConfigurationInstanceCriteriaTransfer;
use Generated\Shared\Transfer\ProductConfigurationInstanceTransfer;
use Spryker\Client\ProductConfigurationStorage\Builder\ProductConfigurationSessionKeyBuilderInterface;
use Spryker\Client\ProductConfigurationStorage\Dependency\Client\ProductConfigurationStorageToSessionClientInterface;
use Spryker\Client\ProductConfigurationStorage\Mapper\ProductConfigurationInstanceMapperInterface;
use Spryker\Client\ProductConfigurationStorage\Storage\ProductConfigurationStorageReaderInterface;

class ProductConfigurationInstanceReader implements ProductConfigurationInstanceReaderInterface
{
    /**
     * @var \Spryker\Client\ProductConfigurationStorage\Storage\ProductConfigurationStorageReaderInterface
     */
    protected ProductConfigurationStorageReaderInterface $configurationStorageReader;

    /**
     * @var \Spryker\Client\ProductConfigurationStorage\Dependency\Client\ProductConfigurationStorageToSessionClientInterface
     */
    protected ProductConfigurationStorageToSessionClientInterface $sessionClient;

    /**
     * @var \Spryker\Client\ProductConfigurationStorage\Mapper\ProductConfigurationInstanceMapperInterface
     */
    protected ProductConfigurationInstanceMapperInterface $productConfigurationStorageMapper;

    /**
     * @var \Spryker\Client\ProductConfigurationStorage\Builder\ProductConfigurationSessionKeyBuilderInterface
     */
    protected ProductConfigurationSessionKeyBuilderInterface $productConfigurationSessionKeyBuilder;

    /**
     * @param \Spryker\Client\ProductConfigurationStorage\Storage\ProductConfigurationStorageReaderInterface $configurationStorageReader
     * @param \Spryker\Client\ProductConfigurationStorage\Dependency\Client\ProductConfigurationStorageToSessionClientInterface $sessionClient
     * @param \Spryker\Client\ProductConfigurationStorage\Mapper\ProductConfigurationInstanceMapperInterface $productConfigurationStorageMapper
     * @param \Spryker\Client\ProductConfigurationStorage\Builder\ProductConfigurationSessionKeyBuilderInterface $productConfigurationSessionKeyBuilder
     */
    public function __construct(
        ProductConfigurationStorageReaderInterface $configurationStorageReader,
        ProductConfigurationStorageToSessionClientInterface $sessionClient,
        ProductConfigurationInstanceMapperInterface $productConfigurationStorageMapper,
        ProductConfigurationSessionKeyBuilderInterface $productConfigurationSessionKeyBuilder
    ) {
        $this->configurationStorageReader = $configurationStorageReader;
        $this->sessionClient = $sessionClient;
        $this->productConfigurationStorageMapper = $productConfigurationStorageMapper;
        $this->productConfigurationSessionKeyBuilder = $productConfigurationSessionKeyBuilder;
    }

    /**
     * @param string $sku
     *
     * @return \Generated\Shared\Transfer\ProductConfigurationInstanceTransfer|null
     */
    public function findProductConfigurationInstanceBySku(string $sku): ?ProductConfigurationInstanceTransfer
    {
        $productConfigurationSessionKey = $this->productConfigurationSessionKeyBuilder->getProductConfigurationSessionKey($sku);
        /** @var \Generated\Shared\Transfer\ProductConfigurationInstanceTransfer|null $productConfigurationInstanceTransfer */
        $productConfigurationInstanceTransfer = $this->sessionClient->get($productConfigurationSessionKey);

        if ($productConfigurationInstanceTransfer) {
            return (new ProductConfigurationInstanceTransfer())
                ->fromArray($productConfigurationInstanceTransfer->toArray());
        }

        return $this->findProductConfigurationInstanceInStorage($sku);
    }

    /**
     * @param string $sku
     *
     * @return \Generated\Shared\Transfer\ProductConfigurationInstanceTransfer|null
     */
    protected function findProductConfigurationInstanceInStorage(string $sku): ?ProductConfigurationInstanceTransfer
    {
        $productConfigurationStorageTransfer = $this->configurationStorageReader
            ->findProductConfigurationStorageBySku($sku);

        if (!$productConfigurationStorageTransfer) {
            return null;
        }

        return $this->productConfigurationStorageMapper
            ->mapProductConfigurationStorageTransferToProductConfigurationInstanceTransfer(
                $productConfigurationStorageTransfer,
                new ProductConfigurationInstanceTransfer(),
            );
    }

    /**
     * @param \Generated\Shared\Transfer\ProductConfigurationInstanceCriteriaTransfer $productConfigurationInstanceCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\ProductConfigurationInstanceCollectionTransfer
     */
    public function getProductConfigurationInstanceCollection(
        ProductConfigurationInstanceCriteriaTransfer $productConfigurationInstanceCriteriaTransfer
    ): ProductConfigurationInstanceCollectionTransfer {
        $productConfigurationInstanceCollectionTransfer = new ProductConfigurationInstanceCollectionTransfer();
        $productConfigurationInstanceConditionsTransfer = $productConfigurationInstanceCriteriaTransfer->getProductConfigurationInstanceConditions();

        if (!$productConfigurationInstanceConditionsTransfer || !$productConfigurationInstanceConditionsTransfer->getSkus()) {
            return $productConfigurationInstanceCollectionTransfer;
        }

        $skus = $productConfigurationInstanceConditionsTransfer->getSkus();
        $productConfigurationInstancesIndexedBySku = $this->getProductConfigurationInstancesFromSession($skus);
        $notConfiguredProductSkus = $this->getNotConfiguredProductSkus(
            $skus,
            array_keys($productConfigurationInstancesIndexedBySku),
        );

        if ($notConfiguredProductSkus) {
            $productConfigurationInstancesIndexedBySku += $this->findProductConfigurationInstancesInStorageIndexedBySku($notConfiguredProductSkus);
        }

        return $productConfigurationInstanceCollectionTransfer->setProductConfigurationInstances(
            new ArrayObject($productConfigurationInstancesIndexedBySku),
        );
    }

    /**
     * @param array<string> $skus
     *
     * @return array<string, \Generated\Shared\Transfer\ProductConfigurationInstanceTransfer>
     */
    protected function getProductConfigurationInstancesFromSession(array $skus): array
    {
        $productConfigurationInstancesIndexedBySku = [];
        $sessionStorageData = $this->sessionClient->all();

        foreach ($skus as $sku) {
            $productConfigurationSessionKey = $this->productConfigurationSessionKeyBuilder->getProductConfigurationSessionKey($sku);
            $productConfigurationInstanceTransfer = $sessionStorageData[$productConfigurationSessionKey] ?? null;

            if (!$productConfigurationInstanceTransfer) {
                continue;
            }

            $productConfigurationInstancesIndexedBySku[$sku] = $productConfigurationInstanceTransfer;
        }

        return $productConfigurationInstancesIndexedBySku;
    }

    /**
     * @param array<string> $skus
     * @param array<string> $configuredProductSkus
     *
     * @return array<string>
     */
    protected function getNotConfiguredProductSkus(array $skus, array $configuredProductSkus): array
    {
        return array_diff($skus, $configuredProductSkus);
    }

    /**
     * @param array<string> $skus
     *
     * @return array<string, \Generated\Shared\Transfer\ProductConfigurationInstanceTransfer>
     */
    protected function findProductConfigurationInstancesInStorageIndexedBySku(array $skus): array
    {
        $productConfigurationStorageTransfers = $this->configurationStorageReader
            ->findProductConfigurationStoragesBySkus($skus);

        return $this->productConfigurationStorageMapper
            ->mapProductConfigurationStorageTransfersToProductConfigurationInstanceTransfersIndexedBySku(
                $productConfigurationStorageTransfers,
            );
    }
}
