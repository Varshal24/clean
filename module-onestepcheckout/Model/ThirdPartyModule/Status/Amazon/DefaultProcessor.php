<?php
namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\Amazon;

use Magento\Framework\ObjectManagerInterface;
use Amazon\Core\Helper\Data;

/**
 * Class DefaultProcessor
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Status\Amazon
 */
class DefaultProcessor implements StatusInterface
{
    /**
     * AbstractHelper Amazon Core Helper Class
     */
    const HELPER_CLASS_NAME = Data::class;

    /**
     * @var Data
     */
    private $amazonHelper;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->amazonHelper = $objectManager->create(self::HELPER_CLASS_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function isPwaEnabled()
    {
        return $this->amazonHelper->isPwaEnabled();
    }
}
