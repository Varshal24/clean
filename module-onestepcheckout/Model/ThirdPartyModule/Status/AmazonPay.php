<?php
namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status;

use Magento\Framework\Module\ModuleListInterface;

/**
 * Class AmazonPay
 */
class AmazonPay
{
    /**
     * Amazon Core Module
     */
    const AMAZON_MODULE_NAME = 'Amazon_Pay';

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @param ModuleListInterface $moduleList
     * @param AmazonVersionPool $amazonVersionPool
     */
    public function __construct(
        ModuleListInterface $moduleList
    ) {
        $this->moduleList = $moduleList;
    }

    /**
     * Check if Amazon module enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->moduleList->has(self::AMAZON_MODULE_NAME);
    }
}