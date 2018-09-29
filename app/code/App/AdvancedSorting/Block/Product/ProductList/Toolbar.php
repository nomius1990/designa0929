<?php
namespace App\AdvancedSorting\Block\Product\ProductList;

class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{
	public function setCollection($collection)
	{
		$this->_collection = $collection;
		$currentOrder = $this->getCurrentOrder();

		switch ($currentOrder) {
			case 'best_seller':
				$this->_collection->sortByBestSellers($this->getCurrentDirectionReverse());
				break;  
			case 'created_at':
				$this->_collection->sortByNew($this->getCurrentDirectionReverse());
				break;
			case 'top_rated': 
				$this->_collection->sortByTopRated($this->getCurrentDirectionReverse());
				break;
			case 'most_viewd':
				$this->_collection->sortByMostViewd($this->getCurrentDirectionReverse());
				break;
			default:
		}

		
		$this->_collection->setCurPage($this->getCurrentPage());
		$limit = (int)$this->getLimit();

		if ($limit) {
			$this->_collection->setPageSize($limit);
		}

		if ($currentOrder) {
			$this->_collection->setOrder($currentOrder, $this->getCurrentDirection());
		}
		
		return $this;
	}

	public function getCurrentDirectionReverse() 
	{
		if ($this->getCurrentDirection() == 'asc') {
			return 'desc';
		} elseif ($this->getCurrentDirection() == 'desc') {
			return 'asc';
		} else {
			return $this->getCurrentDirection();
		}
	}

	public function getTotalNum()
    {
        $totalCount = count($this->getCollection()->getConnection()->fetchAll($this->getCollection()->getSelectCountSql()->__toString()));
        $newCount = $this->getCollection()->getConnection()->fetchOne($this->getCollection()->getSelectCountSql()->__toString());
        $totalCount = $totalCount > $newCount? $totalCount : $newCount;
        return $totalCount;
    }
}