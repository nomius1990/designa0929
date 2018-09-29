<?php

use Magento\Framework\App\Bootstrap;
require _DIR_ . '/app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);

$obj = $bootstrap->getObjectManager();

$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');

$categoryIds = $obj->get('Magento\Catalog\Model\Product')->load(1)
->getCategoryIds();
var_dump($categoryIds);

?>