<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductConfigurationStorage\Replacer;

use Generated\Shared\Transfer\ItemReplaceTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\ProductConfiguratorResponseProcessorResponseTransfer;
use Generated\Shared\Transfer\ProductConfiguratorResponseTransfer;
use Generated\Shared\Transfer\QuoteResponseTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Client\ProductConfigurationStorage\Dependency\Client\ProductConfigurationStorageToCartClientInterface;

class QuoteItemReplacer implements QuoteItemReplacerInterface
{
    protected const GLOSSARY_KEY_PRODUCT_CONFIGURATION_ITEM_NOT_FOUND_IN_CART = 'product_configuration.error.configured_item_not_found_in_cart';

    /**
     * @var \Spryker\Client\ProductConfigurationStorage\Dependency\Client\ProductConfigurationStorageToCartClientInterface
     */
    protected $cartClient;

    /**
     * @param \Spryker\Client\ProductConfigurationStorage\Dependency\Client\ProductConfigurationStorageToCartClientInterface $cartClient
     */
    public function __construct(ProductConfigurationStorageToCartClientInterface $cartClient)
    {
        $this->cartClient = $cartClient;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductConfiguratorResponseTransfer $productConfiguratorResponseTransfer
     * @param \Generated\Shared\Transfer\ProductConfiguratorResponseProcessorResponseTransfer $productConfiguratorResponseProcessorResponseTransfer
     *
     * @return \Generated\Shared\Transfer\ProductConfiguratorResponseProcessorResponseTransfer
     */
    public function replaceItemInQuote(
        ProductConfiguratorResponseTransfer $productConfiguratorResponseTransfer,
        ProductConfiguratorResponseProcessorResponseTransfer $productConfiguratorResponseProcessorResponseTransfer
    ): ProductConfiguratorResponseProcessorResponseTransfer {
        $quoteTransfer = $this->cartClient->getQuote();
        $itemToBeReplacedTransfer = $this->cartClient->findQuoteItem(
            $quoteTransfer,
            $productConfiguratorResponseTransfer->getSku(),
            $productConfiguratorResponseTransfer->getItemGroupKey()
        );

        if (!$itemToBeReplacedTransfer) {
            return $productConfiguratorResponseProcessorResponseTransfer
                ->setIsSuccessful(false)
                ->addMessage($this->createConfigurationItemNotFoundMessage());
        }

        $itemReplaceTransfer = $this->createItemReplaceTransfer(
            $productConfiguratorResponseTransfer,
            $itemToBeReplacedTransfer,
            $quoteTransfer
        );

        $quoteResponseTransfer = $this->cartClient->replaceItem($itemReplaceTransfer);

        if ($quoteResponseTransfer->getIsSuccessful()) {
            return $productConfiguratorResponseProcessorResponseTransfer->setIsSuccessful(true);
        }

        return $this->addQuoteErrors(
            $quoteResponseTransfer,
            $productConfiguratorResponseProcessorResponseTransfer
        );
    }

    /**
     * @param \Generated\Shared\Transfer\ProductConfiguratorResponseTransfer $productConfiguratorResponseTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $itemToBeReplacedTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\ItemReplaceTransfer
     */
    protected function createItemReplaceTransfer(
        ProductConfiguratorResponseTransfer $productConfiguratorResponseTransfer,
        ItemTransfer $itemToBeReplacedTransfer,
        QuoteTransfer $quoteTransfer
    ): ItemReplaceTransfer {
        $newItemTransfer = (new ItemTransfer())
            ->fromArray($itemToBeReplacedTransfer->toArray())
            ->setGroupKey(null)
            ->setProductConfigurationInstance($productConfiguratorResponseTransfer->getProductConfigurationInstance());

        return (new ItemReplaceTransfer())
            ->setItemToBeReplaced($itemToBeReplacedTransfer)
            ->setNewItem($newItemTransfer)
            ->setQuote($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteResponseTransfer $quoteResponseTransfer
     * @param \Generated\Shared\Transfer\ProductConfiguratorResponseProcessorResponseTransfer $productConfiguratorResponseProcessorResponseTransfer
     *
     * @return \Generated\Shared\Transfer\ProductConfiguratorResponseProcessorResponseTransfer
     */
    protected function addQuoteErrors(
        QuoteResponseTransfer $quoteResponseTransfer,
        ProductConfiguratorResponseProcessorResponseTransfer $productConfiguratorResponseProcessorResponseTransfer
    ): ProductConfiguratorResponseProcessorResponseTransfer {
        foreach ($quoteResponseTransfer->getErrors() as $error) {
            $productConfiguratorResponseProcessorResponseTransfer->addMessage(
                (new MessageTransfer())->setValue($error->getMessage())
            );
        }

        return $productConfiguratorResponseProcessorResponseTransfer->setIsSuccessful(false);
    }

    /**
     * @return \Generated\Shared\Transfer\MessageTransfer
     */
    protected function createConfigurationItemNotFoundMessage(): MessageTransfer
    {
        return (new MessageTransfer())
            ->setValue(static::GLOSSARY_KEY_PRODUCT_CONFIGURATION_ITEM_NOT_FOUND_IN_CART);
    }
}
