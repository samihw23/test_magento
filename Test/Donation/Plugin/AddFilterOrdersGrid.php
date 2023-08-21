<?php

declare(strict_types=1);

namespace Test\Donation\Plugin;

use Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory;
use Test\Donation\Model\ResourceModel\Order\Grid\Collection as SalesOrderGridCollection;

/**
 * class to apply filter donated (Yes/NO)
 * class AddFilterOrdersGrid
 * @package Test\Donation\Plugin
 */
class AddFilterOrdersGrid
{
    private SalesOrderGridCollection $collection;

    /**
     * @param SalesOrderGridCollection $collection
     */
    public function __construct(SalesOrderGridCollection $collection
    ) {
        $this->collection = $collection;
    }
    public function aroundGetReport(CollectionFactory $subject, \Closure $proceed, $requestName)
    {
        $result = $proceed($requestName);
        if ($requestName == 'sales_order_grid_data_source' && $result instanceof $this->collection) {
            $select = $this->collection->getSelect();
            $select->columns(['donated' => new \Zend_Db_Expr(
                "case when sales_order.donation_amount > 0 then 'Yes' else 'No' end")]);
            $this->collection->addFilterToMap(
                'donated',
                new \Zend_Db_Expr("case when sales_order.donation_amount > 0 then 'Yes' else 'No' end")
            );
            return $this->collection;
        }
        return $result;
    }
}
