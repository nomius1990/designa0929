<?php
namespace Silk\Cms\Block;
use Magento\Framework\View\Element\Template;

class Reviews extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * Catalog product factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;
    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;
    /**
     * Rating model
     *
     * @var \Magento\Review\Model\RatingFactory
     */
    protected $_ratingFactory;
    /**
     * Review resource model
     *
     * @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory
     */
    protected $_reviewsColFactory;
    /**
     * @var \Magento\Framework\App\Cache
     */
    protected $_cache;
    public function __construct(
        Template\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Framework\App\Cache $cache,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory,
        array $data = [])
    {
        $this->_cache             = $cache;
        $this->_ratingFactory     = $ratingFactory;
        $this->imageHelper        = $imageHelper;
        $this->_productFactory    = $productFactory;
        $this->_storeManager      = $context->getStoreManager();
        $this->_reviewsColFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    public function getReviewLists()
    {
        $cache = $this->_cache->load('review_lists');
        if (empty($cache)) {
            $reviewsCollection = $this->_reviewsColFactory->create();
            $reviewsCollection->addFieldToFilter('score', 5)->addStoreFilter(
                $this->_storeManager->getStore()->getId()
            )->addStatusFilter(
                \Magento\Review\Model\Review::STATUS_APPROVED
            )->setDateOrder()->setPageSize(20);
            $data = [];
            $k = 0;
            foreach ($reviewsCollection as $review) {
                $product = $this->_productFactory->create()->load($review->getEntityPkValue());
                $data['reviews'][$k]['title'] = $review->getTitle();
                $data['reviews'][$k]['detail'] = $review->getDetail();
                $data['reviews'][$k]['created_at'] = $review->getCreatedAt();
                $data['reviews'][$k]['nickname'] = $review->getNickName();
                $data['reviews'][$k]['product_image'] = $this->imageHelper->init(
                    $product,
                    'product_thumbnail_image'
                )->getUrl();
                $data['reviews'][$k]['product_link'] = $product->getProductUrl();
                $k++;
            }

            $reviewsCollections = $this->_reviewsColFactory->create();
            $reviewsCollections->addFieldToFilter('score', 5)->addStoreFilter(
                $this->_storeManager->getStore()->getId()
            )->addStatusFilter(
                \Magento\Review\Model\Review::STATUS_APPROVED
            );

            $data['counts'] = count($reviewsCollections);
            $this->_cache->save(json_encode($data), 'review_lists');
        } else {
            $data = json_decode($cache, true);
        }

        return $data;
    }
}