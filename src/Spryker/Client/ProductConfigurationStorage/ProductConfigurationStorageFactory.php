<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\ProductConfigurationStorage;

use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\ProductConfigurationStorage\Builder\ProductConfigurationSessionKeyBuilder;
use Spryker\Client\ProductConfigurationStorage\Builder\ProductConfigurationSessionKeyBuilderInterface;
use Spryker\Client\ProductConfigurationStorage\Dependency\Client\ProductConfigurationStorageToCartClientInterface;
use Spryker\Client\ProductConfigurationStorage\Dependency\Client\ProductConfigurationStorageToLocaleClientInterface;
use Spryker\Client\ProductConfigurationStorage\Dependency\Client\ProductConfigurationStorageToProductConfigurationClientInterface;
use Spryker\Client\ProductConfigurationStorage\Dependency\Client\ProductConfigurationStorageToProductStorageClientInterface;
use Spryker\Client\ProductConfigurationStorage\Dependency\Client\ProductConfigurationStorageToSessionClientInterface;
use Spryker\Client\ProductConfigurationStorage\Dependency\Client\ProductConfigurationStorageToStorageClientInterface;
use Spryker\Client\ProductConfigurationStorage\Dependency\Service\ProductConfigurationStorageToPriceProductServiceInterface;
use Spryker\Client\ProductConfigurationStorage\Dependency\Service\ProductConfigurationStorageToProductConfigurationServiceInterface;
use Spryker\Client\ProductConfigurationStorage\Dependency\Service\ProductConfigurationStorageToSynchronizationServiceInterface;
use Spryker\Client\ProductConfigurationStorage\Dependency\Service\ProductConfigurationStorageToUtilEncodingServiceInterface;
use Spryker\Client\ProductConfigurationStorage\Expander\PriceProductFilterExpander;
use Spryker\Client\ProductConfigurationStorage\Expander\PriceProductFilterExpanderInterface as PriceProductFilterExpanderInterfaceAlias;
use Spryker\Client\ProductConfigurationStorage\Expander\ProductConfigurationInstanceCartChangeExpander;
use Spryker\Client\ProductConfigurationStorage\Expander\ProductConfigurationInstanceCartChangeExpanderInterface;
use Spryker\Client\ProductConfigurationStorage\Expander\ProductViewExpander;
use Spryker\Client\ProductConfigurationStorage\Expander\ProductViewExpanderInterface;
use Spryker\Client\ProductConfigurationStorage\Extractor\ProductConfigurationVolumePriceExtractor;
use Spryker\Client\ProductConfigurationStorage\Extractor\ProductConfigurationVolumePriceExtractorInterface;
use Spryker\Client\ProductConfigurationStorage\Mapper\ProductConfigurationInstanceMapper;
use Spryker\Client\ProductConfigurationStorage\Mapper\ProductConfigurationInstanceMapperInterface;
use Spryker\Client\ProductConfigurationStorage\Mapper\ProductConfigurationInstancePriceMapper;
use Spryker\Client\ProductConfigurationStorage\Mapper\ProductConfigurationInstancePriceMapperInterface;
use Spryker\Client\ProductConfigurationStorage\Mapper\ProductConfigurationStorageMapper;
use Spryker\Client\ProductConfigurationStorage\Mapper\ProductConfigurationStorageMapperInterface;
use Spryker\Client\ProductConfigurationStorage\Processor\ProductConfiguratorCheckSumResponseProcessor;
use Spryker\Client\ProductConfigurationStorage\Processor\ProductConfiguratorCheckSumResponseProcessorInterface;
use Spryker\Client\ProductConfigurationStorage\Reader\ProductConfigurationAvailabilityReader;
use Spryker\Client\ProductConfigurationStorage\Reader\ProductConfigurationAvailabilityReaderInterface;
use Spryker\Client\ProductConfigurationStorage\Reader\ProductConfigurationInstanceQuoteReader;
use Spryker\Client\ProductConfigurationStorage\Reader\ProductConfigurationInstanceQuoteReaderInterface;
use Spryker\Client\ProductConfigurationStorage\Reader\ProductConfigurationInstanceReader;
use Spryker\Client\ProductConfigurationStorage\Reader\ProductConfigurationInstanceReaderInterface;
use Spryker\Client\ProductConfigurationStorage\Reader\ProductConfigurationPriceReader;
use Spryker\Client\ProductConfigurationStorage\Reader\ProductConfigurationPriceReaderInterface;
use Spryker\Client\ProductConfigurationStorage\Replacer\QuoteItemReplacer;
use Spryker\Client\ProductConfigurationStorage\Replacer\QuoteItemReplacerInterface;
use Spryker\Client\ProductConfigurationStorage\Storage\ProductConfigurationStorageReader;
use Spryker\Client\ProductConfigurationStorage\Storage\ProductConfigurationStorageReaderInterface;
use Spryker\Client\ProductConfigurationStorage\Validator\ProductConfiguratorCheckSumResponseValidatorComposite;
use Spryker\Client\ProductConfigurationStorage\Validator\ProductConfiguratorItemGroupKeyResponseValidator;
use Spryker\Client\ProductConfigurationStorage\Validator\ProductConfiguratorMandatoryFieldsResponseValidator;
use Spryker\Client\ProductConfigurationStorage\Validator\ProductConfiguratorResponseValidatorInterface;
use Spryker\Client\ProductConfigurationStorage\Writer\ProductConfigurationInstanceWriter;
use Spryker\Client\ProductConfigurationStorage\Writer\ProductConfigurationInstanceWriterInterface;

/**
 * @method \Spryker\Client\ProductConfigurationStorage\ProductConfigurationStorageConfig getConfig()
 */
class ProductConfigurationStorageFactory extends AbstractFactory
{
    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Expander\ProductViewExpanderInterface
     */
    public function createProductViewExpander(): ProductViewExpanderInterface
    {
        return new ProductViewExpander(
            $this->createProductConfigurationInstanceReader()
        );
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Mapper\ProductConfigurationInstanceMapperInterface
     */
    public function createProductConfigurationInstanceMapper(): ProductConfigurationInstanceMapperInterface
    {
        return new ProductConfigurationInstanceMapper();
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Extractor\ProductConfigurationVolumePriceExtractorInterface
     */
    public function createProductConfigurationVolumePriceExtractor(): ProductConfigurationVolumePriceExtractorInterface
    {
        return new ProductConfigurationVolumePriceExtractor($this->getUtilEncodingService());
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Mapper\ProductConfigurationInstancePriceMapperInterface
     */
    public function createProductConfigurationInstancePriceMapper(): ProductConfigurationInstancePriceMapperInterface
    {
        return new ProductConfigurationInstancePriceMapper(
            $this->getPriceProductService(),
            $this->getProductConfigurationService(),
            $this->getPriceProductConfigurationStoragePriceExtractorPlugins()
        );
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Reader\ProductConfigurationInstanceQuoteReaderInterface
     */
    public function createProductConfigurationInstanceQuoteReader(): ProductConfigurationInstanceQuoteReaderInterface
    {
        return new ProductConfigurationInstanceQuoteReader($this->getCartClient());
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Reader\ProductConfigurationInstanceReaderInterface
     */
    public function createProductConfigurationInstanceReader(): ProductConfigurationInstanceReaderInterface
    {
        return new ProductConfigurationInstanceReader(
            $this->createProductConfigurationStorageReader(),
            $this->getSessionClient(),
            $this->createProductConfigurationInstanceMapper(),
            $this->createProductConfigurationSessionKeyBuilder()
        );
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Writer\ProductConfigurationInstanceWriterInterface
     */
    public function createProductConfigurationInstanceWriter(): ProductConfigurationInstanceWriterInterface
    {
        return new ProductConfigurationInstanceWriter(
            $this->getSessionClient(),
            $this->createProductConfigurationSessionKeyBuilder()
        );
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Storage\ProductConfigurationStorageReaderInterface
     */
    public function createProductConfigurationStorageReader(): ProductConfigurationStorageReaderInterface
    {
        return new ProductConfigurationStorageReader(
            $this->getSynchronizationService(),
            $this->getStorageClient(),
            $this->createProductConfigurationStorageMapper()
        );
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Mapper\ProductConfigurationStorageMapperInterface
     */
    public function createProductConfigurationStorageMapper(): ProductConfigurationStorageMapperInterface
    {
        return new ProductConfigurationStorageMapper();
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Expander\PriceProductFilterExpanderInterface
     */
    public function createPriceProductFilterExpander(): PriceProductFilterExpanderInterfaceAlias
    {
        return new PriceProductFilterExpander();
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Reader\ProductConfigurationAvailabilityReaderInterface
     */
    public function createProductConfigurationAvailabilityReader(): ProductConfigurationAvailabilityReaderInterface
    {
        return new ProductConfigurationAvailabilityReader(
            $this->createProductConfigurationInstanceReader()
        );
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Processor\ProductConfiguratorCheckSumResponseProcessorInterface
     */
    public function createProductConfiguratorCheckSumResponseProcessor(): ProductConfiguratorCheckSumResponseProcessorInterface
    {
        return new ProductConfiguratorCheckSumResponseProcessor(
            $this->createProductConfigurationInstanceWriter(),
            $this->createProductConfigurationInstancePriceMapper(),
            $this->createQuoteItemReplacer(),
            $this->createProductConfiguratorCheckSumResponseValidatorComposite()
        );
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Replacer\QuoteItemReplacerInterface
     */
    public function createQuoteItemReplacer(): QuoteItemReplacerInterface
    {
        return new QuoteItemReplacer($this->getCartClient());
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Validator\ProductConfiguratorResponseValidatorInterface
     */
    public function createProductConfiguratorCheckSumResponseValidatorComposite(): ProductConfiguratorResponseValidatorInterface
    {
        return new ProductConfiguratorCheckSumResponseValidatorComposite(
            $this->getProductConfigurationClient(),
            $this->createProductConfiguratorCheckSumResponseValidators()
        );
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Reader\ProductConfigurationPriceReaderInterface
     */
    public function createProductConfigurationPriceReader(): ProductConfigurationPriceReaderInterface
    {
        return new ProductConfigurationPriceReader(
            $this->getLocaleClient(),
            $this->getProductStorageClient(),
            $this->createProductConfigurationInstanceReader()
        );
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Expander\ProductConfigurationInstanceCartChangeExpanderInterface
     */
    public function createProductConfigurationInstanceCartChangeExpander(): ProductConfigurationInstanceCartChangeExpanderInterface
    {
        return new ProductConfigurationInstanceCartChangeExpander(
            $this->createProductConfigurationInstanceReader()
        );
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Builder\ProductConfigurationSessionKeyBuilderInterface
     */
    public function createProductConfigurationSessionKeyBuilder(): ProductConfigurationSessionKeyBuilderInterface
    {
        return new ProductConfigurationSessionKeyBuilder();
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Validator\ProductConfiguratorResponseValidatorInterface[]
     */
    public function createProductConfiguratorCheckSumResponseValidators(): array
    {
        return [
            $this->createProductConfiguratorMandatoryFieldsResponseValidator(),
            $this->createProductConfiguratorItemGroupKeyResponseValidator(),
        ];
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Validator\ProductConfiguratorResponseValidatorInterface
     */
    public function createProductConfiguratorMandatoryFieldsResponseValidator(): ProductConfiguratorResponseValidatorInterface
    {
        return new ProductConfiguratorMandatoryFieldsResponseValidator();
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Validator\ProductConfiguratorResponseValidatorInterface
     */
    public function createProductConfiguratorItemGroupKeyResponseValidator(): ProductConfiguratorResponseValidatorInterface
    {
        return new ProductConfiguratorItemGroupKeyResponseValidator();
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Dependency\Client\ProductConfigurationStorageToSessionClientInterface
     */
    public function getSessionClient(): ProductConfigurationStorageToSessionClientInterface
    {
        return $this->getProvidedDependency(ProductConfigurationStorageDependencyProvider::CLIENT_SESSION);
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Dependency\Service\ProductConfigurationStorageToSynchronizationServiceInterface
     */
    public function getSynchronizationService(): ProductConfigurationStorageToSynchronizationServiceInterface
    {
        return $this->getProvidedDependency(ProductConfigurationStorageDependencyProvider::SERVICE_SYNCHRONIZATION);
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Dependency\Service\ProductConfigurationStorageToPriceProductServiceInterface
     */
    public function getPriceProductService(): ProductConfigurationStorageToPriceProductServiceInterface
    {
        return $this->getProvidedDependency(ProductConfigurationStorageDependencyProvider::SERVICE_PRICE_PRODUCT);
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Dependency\Service\ProductConfigurationStorageToUtilEncodingServiceInterface
     */
    public function getUtilEncodingService(): ProductConfigurationStorageToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(ProductConfigurationStorageDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Dependency\Service\ProductConfigurationStorageToProductConfigurationServiceInterface
     */
    public function getProductConfigurationService(): ProductConfigurationStorageToProductConfigurationServiceInterface
    {
        return $this->getProvidedDependency(ProductConfigurationStorageDependencyProvider::SERVICE_PRODUCT_CONFIGURATION);
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Dependency\Client\ProductConfigurationStorageToStorageClientInterface
     */
    public function getStorageClient(): ProductConfigurationStorageToStorageClientInterface
    {
        return $this->getProvidedDependency(ProductConfigurationStorageDependencyProvider::CLIENT_STORAGE);
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Dependency\Client\ProductConfigurationStorageToProductStorageClientInterface
     */
    public function getProductStorageClient(): ProductConfigurationStorageToProductStorageClientInterface
    {
        return $this->getProvidedDependency(ProductConfigurationStorageDependencyProvider::CLIENT_PRODUCT_STORAGE);
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Dependency\Client\ProductConfigurationStorageToLocaleClientInterface
     */
    public function getLocaleClient(): ProductConfigurationStorageToLocaleClientInterface
    {
        return $this->getProvidedDependency(ProductConfigurationStorageDependencyProvider::CLIENT_LOCALE);
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Dependency\Client\ProductConfigurationStorageToCartClientInterface
     */
    public function getCartClient(): ProductConfigurationStorageToCartClientInterface
    {
        return $this->getProvidedDependency(ProductConfigurationStorageDependencyProvider::CLIENT_CART);
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorage\Dependency\Client\ProductConfigurationStorageToProductConfigurationClientInterface
     */
    public function getProductConfigurationClient(): ProductConfigurationStorageToProductConfigurationClientInterface
    {
        return $this->getProvidedDependency(ProductConfigurationStorageDependencyProvider::CLIENT_PRODUCT_CONFIGURATION);
    }

    /**
     * @return \Spryker\Client\ProductConfigurationStorageExtension\Dependency\Plugin\ProductConfigurationStoragePriceExtractorPluginInterface[]
     */
    public function getPriceProductConfigurationStoragePriceExtractorPlugins(): array
    {
        return $this->getProvidedDependency(ProductConfigurationStorageDependencyProvider::PLUGINS_PRODUCT_CONFIGURATION_STORAGE_PRICE_EXTRACTOR);
    }
}
