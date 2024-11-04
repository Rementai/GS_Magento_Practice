<?php
// Logger.php
namespace Training\Sales\Logger;

use Psr\Log\LoggerInterface;

class Logger
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function log($message)
    {
        $this->logger->info($message);
    }
}
