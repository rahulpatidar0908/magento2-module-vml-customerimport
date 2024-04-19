<?php 
namespace VML\CustomerImport\Api;

interface CustomerSaveInterface
{
    /**
     * Save customer data
     *
     * @param array $customerData
     * @return bool
     */
    public function import(string $filePath);
}
