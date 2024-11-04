<?php

namespace Training\Sales\Console\Command;

use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ObjectManager;

class LogTest extends Command
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * LogTest constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        parent::__construct();
    }

    protected function configure() : void
    {
        $this->setName('training:sales:logtest');
        $this->setDescription('Log Test Command');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $output->writeln("Log Test Command Executed");
        $this->logger->info('M2 log information testing ...');

        $nestedArray = [
            ['id' => 1, 'name' => 'Item 1'],
            ['id' => 2, 'name' => 'Item 2']
        ];
        $this->logger->info('Nested array data: ' . json_encode($nestedArray));


        return Cli::RETURN_SUCCESS;
    }
}
