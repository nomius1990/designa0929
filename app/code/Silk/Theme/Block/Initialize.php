<?php
/**
 * Adminhtml AdminNotification inbox grid
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Silk\Theme\Block;

use Magento\Framework\View\Asset\GroupedCollection;
use Magento\Framework\View\Page\Config;

class Initialize extends \Magento\Framework\View\Page\Config\Renderer
{
    protected $pageConfig;

    public function __construct(
        Config $pageConfig,
        \Magento\Framework\View\Asset\MergeService $assetMergeService,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($pageConfig, $assetMergeService, $urlBuilder, $escaper, $string, $logger);
    }

    public function getJsConfig()
    {
        $result = '';

        foreach ($this->pageConfig->getAssetCollection()->getGroups() as $group) {

            $assets = $this->processMerge($group->getAll(), $group);
            $attributes = $this->getGroupAttributes($group);

            foreach ($assets as $asset) {
                $template = $this->getAssetTemplate(
                    $group->getProperty(GroupedCollection::PROPERTY_CONTENT_TYPE),
                    $this->addDefaultAttributes($this->getAssetContentType($asset), $attributes)
                );
                if ($this->getAssetContentType($asset) == 'js') {
                    $result .= sprintf($template, $asset->getUrl());
                }
            }
        }

        return $result;
    }
}