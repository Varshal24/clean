<?php
namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Magento\GiftMessage;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\GiftMessage\Model\CompositeConfigProvider as GiftMessageConfigProvider;

/**
 * Class CheckoutConfigProvider
 *
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Magento\GiftMessage
 */
class CheckoutConfigProvider implements ConfigProviderInterface
{
    /**
     * @var GiftMessageConfigProvider
     */
    private $giftMessageConfigProvider;

    /**
     * @param GiftMessageConfigProvider $giftMessageConfigProvider
     */
    public function __construct(
        GiftMessageConfigProvider $giftMessageConfigProvider
    ) {
        $this->giftMessageConfigProvider = $giftMessageConfigProvider;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'giftOptionsConfig' => $this->giftMessageConfigProvider->getConfig()
        ];
    }
}
