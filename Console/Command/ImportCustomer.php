<?php

declare(strict_types=1);

namespace VML\CustomerImport\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use VML\CustomerImport\Api\CustomerSaveInterface;
use Symfony\Component\Console\Output\OutputInterface;
/**
 * Command to set application mode
 */
class ImportCustomer extends Command
{
    /**
     * Name of "target application mode" input argument
     */
    const PROFILE_NAME = 'profilename';

    const SOURCE = 'source';

    const CSV_FILE = 'csv';

    const JSON_FILE = 'json';
    protected $customerSaveInterface;
    /**
     * Constructor.
     *
     */
    public function __construct(
        CustomerSaveInterface $customerSaveInterface
    ) {
        $this->customerSaveInterface = $customerSaveInterface;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $description = 'Import customer using csv or json file';

        $this->setName('customer:import')
            ->setDescription($description)
            ->setDefinition([
                new InputArgument(
                    self::PROFILE_NAME,
                    InputArgument::REQUIRED,
                    'Enter the profile name'
                ),
                new InputArgument(
                    self::SOURCE,
                    InputArgument::REQUIRED,
                    'Enter Source name'
                )
            ]);
        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $profileName = $input->getArgument(self::PROFILE_NAME);
            $source = $input->getArgument(self::SOURCE);
            //$output->writeln($profileName."==".$source);

            if(!$profileName && !$source) {
                $output->writeln('Please enter prfile name and source name');
                return \Magento\Framework\Console\Cli::RETURN_FAILURE;

            } else {
                $this->customerSaveInterface->import($source);
            }

            $output->writeln('====End=====');

            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
           $output->writeln('====Found error====='.$e->getMessage());
            // we must have an exit code higher than zero to indicate something was wrong
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
    }
}