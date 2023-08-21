<?php

declare(strict_types=1);

namespace Test\Donation\Setup\Patch\Schema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;

/**
 * add donation_amount column to quote and order tables
 *
 * Class AddDonationColumn
 * @package Test\Donation\Setuo\Patch\Schema
 */
class AddDonationColumn implements SchemaPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private ModuleDataSetupInterface $moduleDataSetup;

    private $tableNames = [
        'quote',
        'sales_order'
    ];
    /**
     * Constructor
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }
    /**
     * @return void
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        foreach ($this->tableNames as $tableName) {
            $this->moduleDataSetup->getConnection()->addColumn(
                $this->moduleDataSetup->getTable($tableName),
                'donation_amount',
                [
                    'type' => Table::TYPE_DECIMAL,
                    'nullable' => true,
                    'default' => '0',
                    'comment' => 'Donation Amount'
                ]
            );
        }
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies(): array
    {
        return [];
    }
}
