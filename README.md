Customer import module is used to import the customers by CLI and create customer profiles in bulk.

PHP & Composer versions 
---------------------------
php 8.3
composer 2.0

Installation
============

Official installation method is via composer and its packagist package [ rahulpatidar0908/magento2-module-vml-customerimport](https://packagist.org/packages/rahulpatidar0908/magento2-module-vml-customerimport).

```
$ composer require rahulpatidar0908/magento2-module-vml-customerimport
$ php bin/magento module:enable VML_CustomerImport
$ php bin/magento setup:di:compile
$ php bin/magento setup:static-content:deploy -f

Setup & Configuration
=====================
By default, the temporary directory will be inside vendor directory and will have write permissions from `post_install` composer script.

After successfull installation of module run below commands to import the customers:
====================
$ php bin/magento customer:import CustomerImport-csv CustomerImport.csv
$ php bin/magento customer:import CustomerImport-json CustomerImport.json