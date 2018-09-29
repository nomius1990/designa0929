<?php
namespace Silk\Review\Controller\Adminhtml\Product;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Review\Model\RatingFactory;
use Magento\Review\Model\ReviewFactory;
use Magento\Framework\Controller\ResultFactory;
class Save extends \Magento\Review\Controller\Adminhtml\Product\Save
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;
    /**
     * @var \Magento\Framework\App\Cache
     */
    protected $_cache;
    public function __construct(Context $context, Registry $coreRegistry, ReviewFactory $reviewFactory, RatingFactory $ratingFactory,\Magento\Framework\App\ResourceConnection $resourceConnection,\Magento\Framework\App\Cache $cache)
    {
        $this->_cache             = $cache;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context, $coreRegistry, $reviewFactory, $ratingFactory);
    }
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $this->_cache->remove('review_lists');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (($data = $this->getRequest()->getPostValue()) && ($reviewId = $this->getRequest()->getParam('id'))) {
            $review = $this->reviewFactory->create()->load($reviewId);
            if (!$review->getId()) {
                $this->messageManager->addError(__('The review was removed by another user or does not exist.'));
            } else {
                try {
                    $arrRatingId = $this->getRequest()->getParam('ratings', []);
                    $connection = $this->resourceConnection->getConnection();
                    $select = $connection->select()->from('rating_option', ['option_id', 'value']);
                    $result = $connection->fetchAll($select);
                    $results = [];
                    foreach ($result as $v) {
                        $results[$v['option_id']] = $v['value'];
                    }
                    if (is_array($arrRatingId) && !empty($arrRatingId)) {
                        foreach ($arrRatingId as $value) {
                            $scores[] = $results[$value];
                        }
                    }
                   
                    if (isset($scores)) {
                        $score = ceil(array_sum($scores)/3);
                    } else {
                        $score = 0;
                    }
                    $data['score'] = $score;
                    $review->addData($data)->save();


                    /** @var \Magento\Review\Model\Rating\Option\Vote $votes */
                    $votes = $this->_objectManager->create(\Magento\Review\Model\Rating\Option\Vote::class)
                        ->getResourceCollection()
                        ->setReviewFilter($reviewId)
                        ->addOptionInfo()
                        ->load()
                        ->addRatingOptions();
                    foreach ($arrRatingId as $ratingId => $optionId) {
                        if ($vote = $votes->getItemByColumnValue('rating_id', $ratingId)) {
                            $this->ratingFactory->create()
                                ->setVoteId($vote->getId())
                                ->setReviewId($review->getId())
                                ->updateOptionVote($optionId);
                        } else {
                            $this->ratingFactory->create()
                                ->setRatingId($ratingId)
                                ->setReviewId($review->getId())
                                ->addOptionVote($optionId, $review->getEntityPkValue());
                        }
                    }

                    $review->aggregate();

                    $this->messageManager->addSuccess(__('You saved the review.'));
                } catch (LocalizedException $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while saving this review.'));
                }
            }

            $nextId = (int)$this->getRequest()->getParam('next_item');
            if ($nextId) {
                $resultRedirect->setPath('review/*/edit', ['id' => $nextId]);
            } elseif ($this->getRequest()->getParam('ret') == 'pending') {
                $resultRedirect->setPath('*/*/pending');
            } else {
                $resultRedirect->setPath('*/*/');
            }
            return $resultRedirect;
        }
        $resultRedirect->setPath('review/*/');
        return $resultRedirect;
    }
}