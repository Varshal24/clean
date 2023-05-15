<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Sales\Model\ResourceModel\GridInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;

/**
 * Class RefreshOrderGrid
 */
class RefreshOrderGrid implements DataPatchInterface
{
    private const ORDER_ITEMS_LIMIT = 100;

    /**
     * @var GridInterface
     */
    private GridInterface $entityGrid;

    /**
     * @var OrderCollection
     */
    private OrderCollection $orderCollection;

    /**
     * @param GridInterface $entityGrid
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        GridInterface $entityGrid,
        CollectionFactory $collectionFactory
    ) {
        $this->entityGrid = $entityGrid;
        $this->orderCollection = $collectionFactory->create();
    }

    /**
     * @return string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Add aw_order_note values from sales_order to sales_order_grid table
     *
     * @return $this
     */
    public function apply()
    {
        $offset = 0;

        do {
            $orderIds = $this->orderCollection->getAllIds(self::ORDER_ITEMS_LIMIT, $offset);
            $offset = count($orderIds) ? $offset + count($orderIds) : count($orderIds);
            $this->refreshOrderGrid($orderIds);
        } while ($offset > 0);

        return $this;
    }

    /**
     * Refresh sales_order_grid table
     *
     * @param array $orderIds
     */
    private function refreshOrderGrid(array $orderIds): void
    {
        foreach ($orderIds as $orderId) {
            $this->entityGrid->refresh($orderId);
        }
    }
}
