<?php
namespace Silk\Wishlist\CustomerData;

class Wishlist extends \Magento\Wishlist\CustomerData\Wishlist
{
    public $wishlistHelper;
    public $view;

    public function __construct(
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\Wishlist\Block\Customer\Sidebar $block,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory,
        \Magento\Framework\App\ViewInterface $view
    ) {
        parent::__construct($wishlistHelper, $block, $imageHelperFactory, $view);
        $this->wishlistHelper = $wishlistHelper;
        $this->view = $view;
    }

    protected function getItems()
    {
        $this->view->loadLayout();

        $collection = $this->wishlistHelper->getWishlistItemCollection();
        $collection->clear()->setInStockFilter(true)->setOrder('added_at');

        $items = [];
        foreach ($collection as $wishlistItem) {
            $items[] = $this->getItemData($wishlistItem);
        }
        return $items;
    }
}
