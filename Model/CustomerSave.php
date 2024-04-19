<?php 
namespace VML\CustomerImport\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Module\Dir;
use Magento\Customer\Model\Customer;
use Magento\Framework\File\Csv;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use VML\CustomerImport\Api\CustomerSaveInterface;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Psr\Log\LoggerInterface;
class CustomerSave implements CustomerSaveInterface
{
    const TYPE_ERROR = 'customer entity error';
    protected $customerRepository;
    protected $customerFactory;
    protected $directory_list;
    protected $csv;
    private $errorAggregator;
    private $logger;
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CustomerInterfaceFactory $customerFactory,
        Dir $directory_list,
        Csv $csv,
        ProcessingErrorAggregatorInterface $errorAggregator,
        LoggerInterface $logger
    ) {
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
        $this->directory_list = $directory_list;
        $this->csv = $csv;
        $this->errorAggregator = $errorAggregator;
        $this->logger = $logger;
    }

    public function import($getFile)
    {
        $extension = pathinfo($getFile, PATHINFO_EXTENSION);
        $filePath = $this->directory_list->getDir('VML_CustomerImport');
        $file = $filePath."/CustomerImport/".$getFile;
        
        if ($extension === 'csv') {
            $data = $this->readCsvData($file);
        } elseif ($extension === 'json') {
            $data = $this->readJsonData($file);
        } else {
            throw new LocalizedException(new Phrase('Unsupported file format: ' . $extension));
        }
        try {
            $this->processCustomerData($data);
            return true;
        } catch (LocalizedException $e) {
            $this->errorAggregator->addError($e->getMessage());
            return false;
        }
        
    }
    private function readCsvData(string $filePath): array
    {
        $csvData = $this->csv->getData($filePath);
        if (!$csvData) {
            throw new LocalizedException(new Phrase('Error reading CSV file: ' . $filePath));
        }
        $keys = array_shift($csvData);
        $data = [];
        
        foreach ($csvData as $rowData) {
            // Combine keys with values to create associative array
            $data[] = array_combine($keys, $rowData);
        }
        
        return $data;
    }

    private function readJsonData(string $filePath): array
    {
        $jsonContents = file_get_contents($filePath);
        
        if (!$jsonContents) {
            throw new LocalizedException(new Phrase('Error reading JSON file: ' . $filePath));
        }
        return json_decode($jsonContents, true); // Decoded JSON data
    }

    private function processCustomerData(array $data): void
    {
        
        foreach ($data as $key=>$row) {
            // Validate data (e.g., email format)
            if (!isset($row['emailaddress']) || !filter_var($row['emailaddress'], FILTER_VALIDATE_EMAIL)) {
                throw new LocalizedException(new Phrase('Invalid email format in row: ' . json_encode($row)));
            }

            $customer = $this->customerFactory->create();
            $customer->setFirstname($row['fname']);
            $customer->setLastname($row['lname']);
            $customer->setEmail($row['emailaddress']);

            $customer = $this->customerRepository->save($customer);
    
        }
    }

}
