<?php

namespace Training\Sales\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Console\Cli;

class CrudCommand extends Command
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var State
     */
    private $state;

    /**
     * CrudCommand constructor.
     *
     * @param ResourceConnection $resource
     * @param State $state
     * @param string|null $name
     */
    public function __construct(
        ResourceConnection $resource,
        State $state,
        ?string $name = null
    ) {
        parent::__construct($name);
        $this->resource = $resource;
        $this->state = $state;
    }

    /**
     * Configuration method for the command.
     */
    protected function configure()
    {
        $this->setName('training:sales:crud');
        $this->setDescription('CRUD Operations');
        parent::configure();
    }

    /**
     * Execution method for the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->setAreaCode('frontend');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {

        }

        $connection = $this->resource->getConnection();

        // CREATE
        $data = ['name' => 'Product 11', 'price' => 110.00, 'description' => 'Description 11'];
        $connection->insert('training_sales_table', $data);

        // READ
        $select = $connection->select()->from('training_sales_table')->where('name = ?', 'Product 11');
        $result = $connection->fetchRow($select);
        $output->writeln("Read result:");
        $output->writeln(print_r($result, true));

        // UPDATE
        $connection->update('training_sales_table', ['price' => 115.00], ['name = ?' => 'Product 11']);
        $output->writeln("Updated price to 115.00");

        // DELETE
        $connection->delete('training_sales_table', ['name = ?' => 'Product 9']);
        $output->writeln("Deleted record where name is 'Product 9'");

        $output->writeln("CRUD operations completed.");
        return Cli::RETURN_SUCCESS;
    }
}
