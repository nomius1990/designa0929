<?php
namespace Magefan\Blog\Block;

use Magento\Store\Model\ScopeInterface;
class Index extends \Magefan\Blog\Block\Post\PostList
{
    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->_addBreadcrumbs();
        $this->pageConfig->getTitle()->set($this->_getConfigValue('title'));
        $this->pageConfig->setMetadata('og:image', $this->getViewFileUrl('images/logo.png'));

        return parent::_prepareLayout();
    }

    /**
     * Prepare posts collection
     *
     * @return void
     */
    protected function _preparePostCollection()
    {
        parent::_preparePostCollection();

        $displayMode = $this->_scopeConfig->getValue(
            \Magefan\Blog\Helper\Config::XML_PATH_HOMEPAGE_DISPLAY_MODE,
            ScopeInterface::SCOPE_STORE
        );

        /* If featured posts enabled */
        if ($displayMode == 1) {
            $postIds = $this->_scopeConfig->getValue(
                \Magefan\Blog\Helper\Config::XML_PATH_HOMEPAGE_FEATURED_POST_IDS,
                ScopeInterface::SCOPE_STORE
            );
            $this->_postCollection->addPostsFilter($postIds);
        } else {
            $this->_postCollection->addRecentFilter();
        }
    }

    /**
     * Retrieve blog title
     * @return string
     */
    protected function _getConfigValue($param)
    {
        return $this->_scopeConfig->getValue(
            'mfblog/index_page/'.$param,
            ScopeInterface::SCOPE_STORE
        );
    }


    /**
     * Prepare breadcrumbs
     *
     * @param  string $title
     * @param  string $key
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs($title = null, $key = null)
    {
        if ($breadcrumbsBlock = $this->getBreadcrumbsBlock()) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );

            $blogTitle = $this->_scopeConfig->getValue(
                'mfblog/index_page/title',
                ScopeInterface::SCOPE_STORE
            );
            $breadcrumbsBlock->addCrumb(
                'blog',
                [
                    'label' => __($blogTitle),
                    'title' => __($blogTitle),
                    'link' => null,
                ]
            );
        }
    }
}
