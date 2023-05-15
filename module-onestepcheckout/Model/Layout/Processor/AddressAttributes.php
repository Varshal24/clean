<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor;

use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMetaProvider;
use Aheadworks\OneStepCheckout\Model\Address\Form\Customization\Layout\MultilineModifier;
use Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\Address\Merger;
use Aheadworks\OneStepCheckout\Model\Layout\Processor\AddressAttributes\FieldRowsSorter;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Adds address attributes to shipping and billing sections on checkout
 */
class AddressAttributes implements LayoutProcessorInterface
{
    /**
     * @var AttributeMetaProvider
     */
    private $attributeMataProvider;

    /**
     * @var Merger
     */
    private $attributeMerger;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var FieldRowsSorter
     */
    private $rowsSorter;

    /**
     * @var MultilineModifier
     */
    private $multilineModifier;

    /**
     * @param AttributeMetaProvider $attributeMataProvider
     * @param Merger $attributeMerger
     * @param ArrayManager $arrayManager
     * @param FieldRowsSorter $rowsSorter
     * @param MultilineModifier $multilineModifier
     */
    public function __construct(
        AttributeMetaProvider $attributeMataProvider,
        Merger $attributeMerger,
        ArrayManager $arrayManager,
        FieldRowsSorter $rowsSorter,
        MultilineModifier $multilineModifier
    ) {
        $this->attributeMataProvider = $attributeMataProvider;
        $this->attributeMerger = $attributeMerger;
        $this->arrayManager = $arrayManager;
        $this->rowsSorter = $rowsSorter;
        $this->multilineModifier = $multilineModifier;
    }

    /**
     * {@inheritdoc}
     */
    public function process($jsLayout)
    {
        $shippingAddressFieldRowsPath = 'components/checkout/children/shippingAddress/'
            . 'children/shipping-address-fieldset/children';
        $shippingAddressFieldRowsLayout = $this->arrayManager->get($shippingAddressFieldRowsPath, $jsLayout);
        if ($shippingAddressFieldRowsLayout) {
            $shippingAddressFieldRowsLayout = $this->attributeMerger->merge(
                $this->attributeMataProvider->getMetadata('shipping'),
                'checkoutProvider',
                'shipping',
                $shippingAddressFieldRowsLayout
            );
            $shippingAddressFieldRowsLayout = $this->rowsSorter->sort($shippingAddressFieldRowsLayout, 'shipping');
            $shippingAddressFieldRowsLayout = $this->multilineModifier->modify(
                $shippingAddressFieldRowsLayout,
                'shipping'
            );
            $jsLayout = $this->arrayManager->set(
                $shippingAddressFieldRowsPath,
                $jsLayout,
                $shippingAddressFieldRowsLayout
            );
        }

        $billingAddressFieldRowsPath = 'components/checkout/children/paymentMethod/children/billingAddress/'
            . 'children/billing-address-fieldset/children';
        $billingAddressFieldRowsLayout = $this->arrayManager->get($billingAddressFieldRowsPath, $jsLayout);
        if ($billingAddressFieldRowsLayout) {
            $billingAddressFieldRowsLayout = $this->attributeMerger->merge(
                $this->attributeMataProvider->getMetadata('billing'),
                'checkoutProvider',
                'billing',
                $billingAddressFieldRowsLayout
            );
            $billingAddressFieldRowsLayout = $this->rowsSorter->sort($billingAddressFieldRowsLayout, 'billing');
            $billingAddressFieldRowsLayout = $this->multilineModifier->modify(
                $billingAddressFieldRowsLayout,
                'billing'
            );
            $jsLayout = $this->arrayManager->set(
                $billingAddressFieldRowsPath,
                $jsLayout,
                $billingAddressFieldRowsLayout
            );
        }

        return $jsLayout;
    }
}
