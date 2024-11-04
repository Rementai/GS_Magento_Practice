<?php
namespace Training\Sales\Console\Command;

use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\Exception\LocalizedException;

class HelloWorld extends Command
{
    private const NAME = 'name';

    protected function configure(): void
    {
        $this->setName('training:sales:helloworld');
        $this->setDescription('My first HelloWorld command');
        $this->addOption(
            self::NAME,
            null,
            InputOption::VALUE_OPTIONAL,
            'Name'
        );

        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = 0;

        $name = $input->getOption(self::NAME);
        if ($name) {
            $output->writeln("Hello, " . $name . "!");
        } else {
            $output->writeln("Hello World!");
        }

        try {
            if (!$name) {
                throw new LocalizedException(__('An error occurred because the name was not provided.'));
            }
        } catch (LocalizedException $e) {
            $output->writeln(sprintf(
                '<error>%s</error>',
                $e->getMessage()
            ));
            $exitCode = 1;
        }
        return $exitCode;
    }
}
