<?php
/**
 * Adminhtml AdminNotification inbox grid
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Silk\Framework\Block\Page;

use Magento\Framework\View\Asset\GroupedCollection;

class Renderer extends \Magento\Framework\View\Page\Config\Renderer
{
    protected function renderAssetHtml(\Magento\Framework\View\Asset\PropertyGroup $group)
    {
        $assets = $this->processMerge($group->getAll(), $group);
        $attributes = $this->getGroupAttributes($group);
        
        $result = '';
        try {
            /** @var $asset \Magento\Framework\View\Asset\AssetInterface */
            foreach ($assets as $asset) {
                $template = $this->getAssetTemplate(
                    $group->getProperty(GroupedCollection::PROPERTY_CONTENT_TYPE),
                    $this->addDefaultAttributes($this->getAssetContentType($asset), $attributes)
                );
                if ($this->getAssetContentType($asset) != 'js') {
                    $result .= sprintf($template, $asset->getUrl());
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->critical($e);
            $result .= sprintf($template, $this->urlBuilder->getUrl('', ['_direct' => 'core/index/notFound']));
        }
        return $result;
    }

    protected function addDefaultAttributes($contentType, $attributes)
    {
        switch ($contentType) {
            case 'js':
                $attributes = ' async="true" ' . $attributes;
                break;

            case 'css':
                $attributes = ' rel="stylesheet" ' . ($attributes ?: ' media="all"');
                break;
        }
        return $attributes;
    }
}
