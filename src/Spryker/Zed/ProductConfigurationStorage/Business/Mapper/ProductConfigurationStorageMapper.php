<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductConfigurationStorage\Business\Mapper;

use Generated\Shared\Transfer\ProductConfigurationStorageTransfer;
use Generated\Shared\Transfer\ProductConfigurationTransfer;

class ProductConfigurationStorageMapper implements ProductConfigurationStorageMapperInterface
{
    public function mapProductConfigurationTransferToProductConfigurationStorageTransfer(
        ProductConfigurationTransfer $productConfiguration,
        ProductConfigurationStorageTransfer $productConfigurationStorageTransfer
    ): ProductConfigurationStorageTransfer {
        $productConfigurationStorageTransfer->fromArray($productConfiguration->toArray(), true);
        $productConfigurationStorageTransfer->setFkProductConfiguration($productConfiguration->getIdProductConfiguration());

        return $productConfigurationStorageTransfer;
    }
}
