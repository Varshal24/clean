<?php
namespace Aheadworks\OneStepCheckout\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class RestoreToDefaultConfigData
 */
class RestoreToDefaultConfigData implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Restore to default config values
     */
    public function apply()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $configTable = $this->moduleDataSetup->getTable('core_config_data');
        $connection->delete($configTable, ['path = ?' => 'sales/totals_sort/customerbalance']);
        $connection->delete($configTable, ['path = ?' => 'sales/totals_sort/giftcardaccount']);
        $connection->delete($configTable, ['path = ?' => 'sales/totals_sort/reward']);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }
}
