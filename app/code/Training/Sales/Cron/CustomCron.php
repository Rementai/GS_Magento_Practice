<?php
namespace Training\Sales\Cron;

use Psr\Log\LoggerInterface;

class CustomCron
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function execute()
    {
        $this->logger->info('Custom cron job executed.');
        return $this;
    }
}
