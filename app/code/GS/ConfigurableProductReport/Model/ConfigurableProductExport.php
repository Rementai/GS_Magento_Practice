<?php

declare(strict_types=1);

/**
 * GS ConfigurableProductReport
 *
 * @copyright Copyright (c) 2024 Gate-Software Sp. z o.o. (www.gate-software.com). All rights reserved.
 * @author    Gate-Software Practice Team
 * @author    sebastian.mazgaj@gate-commerce.com
 *
 * @package   GS_ConfigurableProductReport
 */

namespace GS\ConfigurableProductReport\Model;

use GS\ConfigurableProductReport\Model\Product as ConfigurableProductReportProduct;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Filesystem\Io\File;

class ConfigurableProductExport
{
    protected const DELIMITER = ',';
    protected const ENCLOSURE = '"';
    protected const CATEGORY_SEPARATOR = ';';
    protected const PRICE_SEPARATOR = '.';
    protected const EXPORT_FILEPATH = 'export/configurable_products_report.csv';

    private const PRODUCT_ATTRIBUTES = ['sku', 'name', 'price'];
    private const FILE_OPEN_MODE = 'w+';
    private const CSV_HEADERS = ['ID', 'SKU', 'Name', 'Price', 'Categories'];

    /**
     * Constructor
     *
     * @param CollectionFactory $productCollectionFactory
     * @param DirectoryList $directoryList
     * @param File $file
     * @param FileDriver $fileDriver
     */
    public function __construct(
        protected CollectionFactory $productCollectionFactory,
        protected DirectoryList $directoryList,
        protected File $file,
        protected FileDriver $fileDriver
    ) {
    }

    /**
     * Export method handles data to export
     *
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function export(): void
    {
        $collection = $this->getProductCollection();
        if ($collection->getSize() === 0) {
            return;
        }

        $handle = $this->createFileHandle($this->getFilePath());
        $this->writeCsvHeader($handle);

        $this->writeProductDataToCsv($handle, $collection);

        $this->fileDriver->fileClose($handle);
    }

    /**
     * @param Resource $handle
     * @param Collection $collection
     * @return void
     * @throws FileSystemException
     */
    protected function writeProductDataToCsv(Resource $handle, Collection $collection): void
    {
        /* @var Product $product */
        foreach ($collection as $product) {
            $this->writeCsvRow($handle, $this->prepareProductData($product));
        }
    }

    /**
     * Get the path for the CSV file
     *
     * @return string
     * @throws FileSystemException
     */
    protected function getFilePath(): string
    {
        return $this->directoryList->getPath(DirectoryList::VAR_DIR) . DIRECTORY_SEPARATOR . self::EXPORT_FILEPATH;
    }

    /**
     * Get the collection of configurable products
     *
     * @return Collection
     */
    protected function getProductCollection(): Collection
    {
        return $this->productCollectionFactory->create()
            ->addAttributeToSelect(self::PRODUCT_ATTRIBUTES)
            ->addAttributeToFilter(
                ConfigurableProductReportProduct::ATTRIBUTE_TYPE_ID,
                ConfigurableProductReportProduct::TYPE_CONFIGURABLE
            );
    }

    /**
     * Create file handle for the CSV file
     *
     * @param string $filePath
     * @return Resource
     * @throws FileSystemException
     * @throws LocalizedException
     */
    protected function createFileHandle(string $filePath)
    {
        $this->file->checkAndCreateFolder(dirname($filePath));

        return $this->fileDriver->fileOpen($filePath, self::FILE_OPEN_MODE);
    }

    /**
     * Write the CSV header row
     *
     * @param Resource $handle
     * @return void
     * @throws FileSystemException
     */
    protected function writeCsvHeader(Resource $handle): void
    {
        $this->fileDriver->filePutCsv($handle, self::CSV_HEADERS, self::DELIMITER, self::ENCLOSURE);
    }

    /**
     * Prepare the product data for CSV export
     *
     * @param Product $product
     * @return array<int, string, string, string, string>
     */
    protected function prepareProductData(Product $product): array
    {
        return [
            $product->getId(),
            $product->getSku(),
            $product->getName(),
            $this->getParsedProductPrice($product),
            $this->getProductCategories($product)
        ];
    }

    /**
     * Gets price and formats it to decimal
     *
     * @param Product $product
     * @return string
     */
    protected function getParsedProductPrice(Product $product): string
    {
        return number_format($this->getProductPrice($product), 2, self::PRICE_SEPARATOR);
    }

    /**
     * Get the price of the product, fallback to child products if necessary
     *
     * @param Product $product
     * @return float
     */
    protected function getProductPrice(Product $product): float
    {
        $price = $product->getPrice();
        if ($price > 0) {
            return $price;
        }

        $childProducts = $product->getTypeInstance()->getUsedProducts($product);
        if (empty($childProducts)) {
            return 0.0;
        }

        return $this->getChildProductPrice($childProducts);
    }

    /**
     * @param Product[] $childProducts
     * @return float
     */
    protected function getChildProductPrice(array $childProducts): float
    {
        foreach ($childProducts as $childProduct) {
            $price = $childProduct->getPrice();
            if ($price) {
                return (float)$price;
            }
        }

        return 0.0;
    }

    /**
     * Get the category IDs of the product as a string
     *
     * @param Product $product
     * @return string
     */
    protected function getProductCategories(Product $product): string
    {
        return implode(self::CATEGORY_SEPARATOR, $product->getCategoryIds());
    }

    /**
     * Write a single row to the CSV file
     *
     * @param Resource $handle
     * @param array<int, string|int|float> $data
     * @return void
     * @throws FileSystemException
     */
    protected function writeCsvRow(Resource $handle, array $data): void
    {
        $this->fileDriver->filePutCsv($handle, $data, self::DELIMITER, self::ENCLOSURE);
    }
}
