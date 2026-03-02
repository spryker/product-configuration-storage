<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductConfigurationStorage\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\ProductConfigurationStorage\Business\Deleter\ProductConfigurationStorageDeleter;
use Spryker\Zed\ProductConfigurationStorage\Business\Deleter\ProductConfigurationStorageDeleterInterface;
use Spryker\Zed\ProductConfigurationStorage\Business\Mapper\ProductConfigurationStorageMapper;
use Spryker\Zed\ProductConfigurationStorage\Business\Mapper\ProductConfigurationStorageMapperInterface;
use Spryker\Zed\ProductConfigurationStorage\Business\Writer\ProductConfigurationStorageWriter;
use Spryker\Zed\ProductConfigurationStorage\Business\Writer\ProductConfigurationStorageWriterInterface;
use Spryker\Zed\ProductConfigurationStorage\Dependency\Facade\ProductConfigurationStorageToEventBehaviorFacadeInterface;
use Spryker\Zed\ProductConfigurationStorage\Dependency\Facade\ProductConfigurationStorageToProductConfigurationFacadeInterface;
use Spryker\Zed\ProductConfigurationStorage\ProductConfigurationStorageDependencyProvider;

/**
 * @method \Spryker\Zed\ProductConfigurationStorage\ProductConfigurationStorageConfig getConfig()
 * @method \Spryker\Zed\ProductConfigurationStorage\Persistence\ProductConfigurationStorageRepositoryInterface getRepository()
 * @method \Spryker\Zed\ProductConfigurationStorage\Persistence\ProductConfigurationStorageEntityManagerInterface getEntityManager()
 */
class ProductConfigurationStorageBusinessFactory extends AbstractBusinessFactory
{
    public function createProductConfigurationStorageWriter(): ProductConfigurationStorageWriterInterface
    {
        return new ProductConfigurationStorageWriter(
            $this->getEventBehaviorFacade(),
            $this->getProductConfigurationFacade(),
            $this->createProductConfigurationStorageMapper(),
            $this->getEntityManager(),
        );
    }

    public function createProductConfigurationStorageDeleter(): ProductConfigurationStorageDeleterInterface
    {
        return new ProductConfigurationStorageDeleter(
            $this->getEventBehaviorFacade(),
            $this->getEntityManager(),
        );
    }

    public function createProductConfigurationStorageMapper(): ProductConfigurationStorageMapperInterface
    {
        return new ProductConfigurationStorageMapper();
    }

    public function getProductConfigurationFacade(): ProductConfigurationStorageToProductConfigurationFacadeInterface
    {
        return $this->getProvidedDependency(ProductConfigurationStorageDependencyProvider::FACADE_PRODUCT_CONFIGURATION);
    }

    public function getEventBehaviorFacade(): ProductConfigurationStorageToEventBehaviorFacadeInterface
    {
        return $this->getProvidedDependency(ProductConfigurationStorageDependencyProvider::FACADE_EVENT_BEHAVIOR);
    }
}
