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

namespace GS\ConfigurableProductReport\Cron;

use GS\ConfigurableProductReport\Model\ConfigurableProductExport;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;

class GenerateReport
{
    /**
     * Constructor
     *
     * @param ConfigurableProductExport $configurableProductExport
     */
    public function __construct(
        protected ConfigurableProductExport $configurableProductExport
    ) {}

    /**
     * Execute method to export class object
     *
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function execute(): void
    {
        $this->configurableProductExport->export();
    }
}
