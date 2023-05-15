<?php
namespace Aheadworks\OneStepCheckout\Plugin\Checkout\Controller\Index;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Forward;
use Magento\Checkout\Controller\Index\Index as CheckoutIndex;
use Aheadworks\OneStepCheckout\Model\Config;

/**
 * Class Index
 * @package Aheadworks\OneStepCheckout\Plugin\Checkout\Controller\Index
 */
class Index
{
    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ResultFactory $resultFactory
     * @param Manager $moduleManager
     */
    public function __construct(
        ResultFactory $resultFactory,
        Config $config
    ) {
        $this->resultFactory = $resultFactory;
        $this->config = $config;
    }

    /**
     * Perform forward to one step checkout action if needed
     *
     * @param CheckoutIndex $subject
     * @param \Closure $proceed
     * @return ResultInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundExecute(CheckoutIndex $subject, \Closure $proceed)
    {
        if ($this->isNeedToPerformForwardToOneStepCheckout()) {
            /** @var Forward $resultForward */
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $result = $resultForward
                ->setModule('onestepcheckout')
                ->setController('index')
                ->forward('index');
        } else {
            $result = $proceed();
        }
        return $result;
    }

    /**
     * Check if need to perform forward to one step checkout
     *
     * @return bool
     */
    private function isNeedToPerformForwardToOneStepCheckout()
    {
        return $this->config->isEnabled();
    }
}
