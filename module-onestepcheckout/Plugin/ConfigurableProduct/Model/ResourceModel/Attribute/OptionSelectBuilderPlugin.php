<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Plugin\ConfigurableProduct\Model\ResourceModel\Attribute;

use Magento\ConfigurableProduct\Model\ResourceModel\Attribute\OptionSelectBuilderInterface;
use Magento\Framework\DB\Select;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\RequestInterface;
use Aheadworks\OneStepCheckout\Model\BaseFactory;

/**
 * Class OptionSelectBuilderPlugin
 */
class OptionSelectBuilderPlugin
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var BaseFactory
     */
    private $stockResolver;

    /**
     * @var BaseFactory
     */
    private $stockIndexTableNameResolver;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param StoreManagerInterface $storeManager
     * @param BaseFactory $stockResolver
     * @param BaseFactory $stockIndexTableNameResolver
     * @param RequestInterface $request
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        BaseFactory $stockResolver,
        BaseFactory $stockIndexTableNameResolver,
        RequestInterface $request
    ) {
        $this->storeManager = $storeManager;
        $this->stockResolver = $stockResolver;
        $this->stockIndexTableNameResolver = $stockIndexTableNameResolver;
        $this->request = $request;
    }

    /**
     * Add "is_salable" filter to select for checkout page if it needs to be done.
     *
     * @param OptionSelectBuilderInterface $subject
     * @param Select $select
     * @return Select
     */
    public function afterGetSelect(OptionSelectBuilderInterface $subject, Select $select): Select
    {
        if ($this->isFilterShouldBeAdded($select)) {
            $this->addInStockFilter($select);
        }

        return $select;
    }

    /**
     * Check that this is an OSC page and that the filter was not added in another plugin
     *
     * @param Select $select
     * @return bool
     */
    private function isFilterShouldBeAdded(Select $select): bool
    {
        try {
            if ($this->request->getControllerModule() == 'Aheadworks_OneStepCheckout') {
                $parts = $select->getPart(Select::FROM);
                $result = !array_key_exists('stock', $parts);
            } else {
                $result = false;
            }
        } catch (\Zend_Db_Select_Exception $e) {
            $result = false;
        }

        return $result;
    }

    /**
     * Add "is_salable" filter.
     *
     * @param Select $select
     * @return void
     */
    private function addInStockFilter(Select $select): void
    {
        $websiteCode = $this->storeManager->getWebsite()->getCode();
        $stockResolver = $this->stockResolver->create();
        $stockIndexTableNameResolver = $this->stockIndexTableNameResolver->create();

        if ($stockResolver && $stockIndexTableNameResolver) {
            $stock = $stockResolver->execute(SalesChannelInterface::TYPE_WEBSITE, $websiteCode);
            $stockId = (int)$stock->getStockId();
            $stockTable = $stockIndexTableNameResolver->execute($stockId);

            $select->joinInner(
                ['stock' => $stockTable],
                'stock.sku = entity.sku',
                []
            )->where(
                'stock.is_salable = ?',
                1
            );
        }
    }
}
