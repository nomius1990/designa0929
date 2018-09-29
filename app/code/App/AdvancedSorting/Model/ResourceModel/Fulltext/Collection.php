<?php
namespace App\AdvancedSorting\Model\ResourceModel\Fulltext;

class Collection extends \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection 
{
	function sortByBestSellers($dir) {
		$this->getSelect()->joinLeft(
			'sales_order_item',
			'e.entity_id = sales_order_item.product_id',
			array('qty_ordered'=>'SUM(sales_order_item.qty_ordered)'))
		     ->group('e.entity_id')
		     ->order('qty_ordered ' . $dir);
	}

	function sortByNew($dir) {
		$this->getSelect()
		     ->order('e.created_at ' . $dir);
	}

	function sortByTopRated($dir) {
		$this->getSelect()->joinLeft(
			'rating_option_vote_aggregated',
			'e.entity_id = rating_option_vote_aggregated.entity_pk_value',
			array('vote_count' => 'SUM(rating_option_vote_aggregated.vote_count)'))
			  ->group('e.entity_id')
			  ->order('vote_count ' . $dir);
	}

	function sortByMostViewd($dir) {
		$this->getSelect()->joinInner(
			'report_event',
			'e.entity_id = report_event.object_id',
			array('event_id'=>'SUM(report_event.event_id)'))
			  ->group('e.entity_id')
			  ->order('event_id ' . $dir);
	}
}