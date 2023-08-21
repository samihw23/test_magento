<?php

declare(strict_types=1);

namespace Test\Donation\Model\ResourceModel\Order\Grid;

use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OriginalCollection;

/**
 * Order grid extended collection
 *
 * class Collection
 * @package Test\Donation\Model\ResourceModel\Order\Grid
 */
class Collection extends OriginalCollection
{
    /**
     * @return void
     */
    protected function _renderFiltersBefore(): void
    {
        $joinTable = $this->getTable('sales_order');
        $this->getSelect()->joinLeft($joinTable, 'main_table.entity_id = sales_order.entity_id', ['donation_amount']);
        parent::_renderFiltersBefore();
    }
}
