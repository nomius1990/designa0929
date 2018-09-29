<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Silk\Review\Block\Product;

class Review extends \Magento\Review\Block\Product\Review
{
    public function setTabTitle()
    {
        $title = __('Reviews');
        $this->setTitle($title);
    }
}
