<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductConfigurationStorage;

use Generated\Shared\Transfer\PriceProductFilterTransfer;
use Generated\Shared\Transfer\ProductConfigurationInstanceCollectionTransfer;
use Generated\Shared\Transfer\ProductConfigurationInstanceCriteriaTransfer;
use Generated\Shared\Transfer\ProductConfigurationInstanceTransfer;
use Generated\Shared\Transfer\ProductStorageCriteriaTransfer;
use Generated\Shared\Transfer\ProductViewTransfer;

interface ProductConfigurationStorageClientInterface
{
    /**
     * Specification:
     * - Stores ProductConfigurationInstanceTransfer in the session by SKU.
     *
     * @api
     *
     * @param string $sku
     * @param \Generated\Shared\Transfer\ProductConfigurationInstanceTransfer $productConfigurationInstanceTransfer
     *
     * @return void
     */
    public function storeProductConfigurationInstanceBySku(
        string $sku,
        ProductConfigurationInstanceTransfer $productConfigurationInstanceTransfer
    ): void;

    /**
     * Specification:
     * - Expands the product view with the product configuration data.
     * - Expects ProductViewTransfer::sku property to be provided.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductViewTransfer $productViewTransfer
     * @param array<string, mixed> $productData
     * @param string $localeName
     * @param \Generated\Shared\Transfer\ProductStorageCriteriaTransfer|null $productStorageCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\ProductViewTransfer
     */
    public function expandProductViewWithProductConfigurationInstance(
        ProductViewTransfer $productViewTransfer,
        array $productData,
        string $localeName,
        ?ProductStorageCriteriaTransfer $productStorageCriteriaTransfer = null
    ): ProductViewTransfer;

    /**
     * Specification:
     * - Retrieves current store specific product concrete storage data by id.
     * - Retrieves product configuration instance by product concrete SKU.
     * - Returns product configuration prices or empty array if prices weren't set.
     *
     * @api
     *
     * @param int $idProductConcrete
     *
     * @return array<\Generated\Shared\Transfer\PriceProductTransfer>
     */
    public function findProductConcretePricesByIdProductConcrete(int $idProductConcrete): array;

    /**
     * Specification:
     * - Retrieves product configuration instance from product view.
     * - Expands price product filter with product configuration instance.
     * - Returns expanded price product filter.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductViewTransfer $productViewTransfer
     * @param \Generated\Shared\Transfer\PriceProductFilterTransfer $priceProductFilterTransfer
     *
     * @return \Generated\Shared\Transfer\PriceProductFilterTransfer
     */
    public function expandPriceProductFilterWithProductConfigurationInstance(
        ProductViewTransfer $productViewTransfer,
        PriceProductFilterTransfer $priceProductFilterTransfer
    ): PriceProductFilterTransfer;

    /**
     * Specification:
     * - Checks product view for the product configuration instance existence.
     * - Returns true if exist.
     * - Makes attempt to find it by SKU.
     * - Returns true if found, false otherwise.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductViewTransfer $productViewTransfer
     *
     * @return bool
     */
    public function isProductHasProductConfigurationInstance(ProductViewTransfer $productViewTransfer): bool;

    /**
     * Specification:
     * - Returns true if product concrete configuration is available for given product view transfer or false otherwise.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductViewTransfer $productViewTransfer
     *
     * @return bool
     */
    public function isProductConcreteAvailable(ProductViewTransfer $productViewTransfer): bool;

    /**
     * Specification:
     * - Fetches a collection of product configuration instances at first from session and then from storage if not found.
     * - Uses `ProductConfigurationInstanceCriteriaTransfer.ProductConfigurationInstanceConditions.skus` to filter product configuration instances by skus.
     * - Returns empty `ProductConfigurationInstanceCollectionTransfer` while no `ProductConfigurationInstanceCriteriaTransfer.ProductConfigurationInstanceConditions.skus` defined.
     * - Returns `ProductConfigurationInstanceCollectionTransfer` filled with found product configuration instances indexed by `ProductConfigurationInstanceCriteriaTransfer.ProductConfigurationInstanceConditions.skus`.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductConfigurationInstanceCriteriaTransfer $productConfigurationInstanceCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\ProductConfigurationInstanceCollectionTransfer
     */
    public function getProductConfigurationInstanceCollection(
        ProductConfigurationInstanceCriteriaTransfer $productConfigurationInstanceCriteriaTransfer
    ): ProductConfigurationInstanceCollectionTransfer;
}
