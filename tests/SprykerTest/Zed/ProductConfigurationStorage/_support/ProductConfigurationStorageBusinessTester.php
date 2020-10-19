<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductConfigurationStorage;

use Codeception\Actor;
use Orm\Zed\ProductConfigurationStorage\Persistence\SpyProductConfigurationStorageQuery;

/**
 * Inherited Methods
 *
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 * @method \Spryker\Zed\ProductConfigurationStorage\Business\ProductConfigurationStorageFacadeInterface getFacade()
 *
 * @SuppressWarnings(PHPMD)
 */
class ProductConfigurationStorageBusinessTester extends Actor
{
    use _generated\ProductConfigurationStorageBusinessTesterActions;

    /**
     * @return void
     */
    public function truncateProductConfigurationStorageEntities(): void
    {
        $this->truncateTableRelations($this->getProductConfigurationStorageQuery());
    }

    /**
     * @return int
     */
    public function countProductConfigurationStorageEntities(): int
    {
        return $this->getProductConfigurationStorageQuery()->count();
    }

    /**
     * @return \Orm\Zed\ProductConfigurationStorage\Persistence\SpyProductConfigurationStorageQuery
     */
    protected function getProductConfigurationStorageQuery(): SpyProductConfigurationStorageQuery
    {
        return SpyProductConfigurationStorageQuery::create();
    }
}
