<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductConfigurationStorage\Dependency\Client;

use Generated\Shared\Transfer\ItemReplaceTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

class ProductConfigurationStorageToCartClientBridge implements ProductConfigurationStorageToCartClientInterface
{
    /**
     * @var \Spryker\Client\Cart\CartClientInterface
     */
    protected $cartClient;

    /**
     * @param \Spryker\Client\Cart\CartClientInterface $cartClient
     */
    public function __construct($cartClient)
    {
        $this->cartClient = $cartClient;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param string $sku
     * @param string|null $groupKey
     *
     * @return \Generated\Shared\Transfer\ItemTransfer|null
     */
    public function findQuoteItem(QuoteTransfer $quoteTransfer, string $sku, ?string $groupKey = null): ?ItemTransfer
    {
        return $this->cartClient->findQuoteItem($quoteTransfer, $sku, $groupKey);
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function getQuote()
    {
        return $this->cartClient->getQuote();
    }

    /**
     * @param \Generated\Shared\Transfer\ItemReplaceTransfer $itemReplaceTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteResponseTransfer
     */
    public function replaceItem(ItemReplaceTransfer $itemReplaceTransfer): QuoteResponseTransfer
    {
        return $this->cartClient->replaceItem($itemReplaceTransfer);
    }
}
