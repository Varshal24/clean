<?php
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Aheadworks\OneStepCheckout\Model\Customer\Form\AttributeMetaProvider;
use Aheadworks\OneStepCheckout\Model\Layout\Processor\Attributes\Customer\Merger;
use Aheadworks\OneStepCheckout\Model\Layout\Processor\CustomerAttributes\FieldRowsSorter;

/**
 * Adds customer attributes to customer information section on checkout
 */
class CustomerAttributes implements LayoutProcessorInterface
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
     * @var FieldRowsSorter
     */
    private $rowsSorter;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param AttributeMetaProvider $attributeMataProvider
     * @param Merger $attributeMerger
     * @param FieldRowsSorter $rowsSorter
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        AttributeMetaProvider $attributeMataProvider,
        Merger $attributeMerger,
        FieldRowsSorter $rowsSorter,
        ArrayManager $arrayManager
    ) {
        $this->attributeMataProvider = $attributeMataProvider;
        $this->attributeMerger = $attributeMerger;
        $this->rowsSorter = $rowsSorter;
        $this->arrayManager = $arrayManager;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function process($jsLayout)
    {
        $customerInfoFieldRowsPath = 'components/checkout/children/customer-information/children/'
            . 'customer-info-fields/children/customer-info-fieldset/children';
        $customerInfoFieldRowsLayout = $this->attributeMerger->merge(
            $this->attributeMataProvider->getMetadata(),
            'checkoutProvider'
        );
        $customerInfoFieldRowsLayout = $this->rowsSorter->sort($customerInfoFieldRowsLayout);
        $jsLayout = $this->arrayManager->set(
            $customerInfoFieldRowsPath,
            $jsLayout,
            $customerInfoFieldRowsLayout
        );

        return $jsLayout;
    }
}
