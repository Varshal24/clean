<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;

/**
 * Class BeforePaymentExtraOptions
 *
 * @package Aheadworks\OneStepCheckout\Model\Layout\Processor
 */
class BeforePaymentExtraOptions implements LayoutProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var array
     */
    private $componentsToMove;

    /**
     * @param ArrayManager $arrayManager
     * @param array $componentsToMove
     */
    public function __construct(
        ArrayManager $arrayManager,
        array $componentsToMove
    ) {
        $this->arrayManager = $arrayManager;
        $this->componentsToMove = $componentsToMove;
    }

    /**
     * @inheritdoc
     */
    public function process($jsLayout)
    {
        $paymentOptionsPath = 'components/checkout/children/payment-options/children';
        $paymentOptions = $this->arrayManager->get($paymentOptionsPath, $jsLayout);

        $paymentOptionsToMove = [];
        foreach ($paymentOptions as $key => $paymentOptionData) {
            if (in_array($key, $this->componentsToMove)) {
                $paymentOptionsToMove[$key] = $paymentOptionData;
                $jsLayout = $this->arrayManager->remove($paymentOptionsPath . '/' . $key, $jsLayout);
            }
        }

        $beforePaymentMethodPath = 'components/checkout/children/paymentMethod/children/extraPaymentOptions/children';
        return $this->arrayManager->merge($beforePaymentMethodPath, $jsLayout, $paymentOptionsToMove);
    }
}
