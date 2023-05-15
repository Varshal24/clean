<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\CustomerInfo;

use Aheadworks\OneStepCheckout\Model\Config\Source\Customization\CustomValidation;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\BaseCustomization;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\ModuleManager;
use Aheadworks\OneStepCheckout\Model\Customer\Form\AttributeMeta\AvailabilityChecker;
use Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow\DefaultSortOrder;
use Aheadworks\OneStepCheckout\Model\Attribute\Form\AttributeMeta\Provider as AttributeMetadataProvider;
use Aheadworks\OneStepCheckout\Model\Config\Source\Customization\BooleanMetaField;

/**
 * Customer info block for form customization
 */
class Customization extends BaseCustomization
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'system/config/customer_info/customization.phtml';

    /**
     * @param Context $context
     * @param AttributeMetadataProvider $attributeMetadata
     * @param AvailabilityChecker $availabilityChecker
     * @param DefaultSortOrder $defaultSortOrder
     * @param ModuleManager $moduleManager
     * @param BooleanMetaField $booleanMetaField
     * @param CustomValidation $customValidation
     * @param array $data
     */
    public function __construct(
        protected Context $context,
        protected AttributeMetadataProvider $attributeMetadata,
        private AvailabilityChecker $availabilityChecker,
        protected DefaultSortOrder $defaultSortOrder,
        protected ModuleManager $moduleManager,
        protected BooleanMetaField $booleanMetaField,
        protected CustomValidation $customValidation,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $attributeMetadata,
            $moduleManager,
            $defaultSortOrder,
            $booleanMetaField,
            $customValidation,
            $data
        );
    }

    /**
     * Get attribute codes grouped by field row ids
     *
     * @return array
     * @throws LocalizedException
     */
    public function getAttrCodesGroupedByFieldRow()
    {
        $data = [];
        $prevSortOrder = null;
        $attributes = $this->attributeMetadata->getAttributeMetadataList('customer_account_create');
        foreach ($attributes as $attributesMeta) {
            if (!$this->availabilityChecker->isAvailableOnForm($attributesMeta)) {
                continue;
            }
            $attributeCode = $attributesMeta->getAttributeCode();
            if (!isset($data[$attributeCode])) {
                $data[$attributeCode] = [];
            }
            $data[$attributeCode][] = $attributeCode;
        }
        uksort($data, [$this, 'compareFieldRows']);
        return $data;
    }

    /**
     * Get attribute form values
     *
     * @param string $attributeCode
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getAttributeFormValues($attributeCode)
    {
        $value = $this->getElement()->getValue();
        if (isset($value['attributes'][$attributeCode])) {
            $formValues = $value['attributes'][$attributeCode];
            if (array_key_exists(0, $formValues)) {
                $formValues = $formValues[0];
            }
            $formValues[BooleanMetaField::IS_MOVED] = $formValues[BooleanMetaField::IS_MOVED] ?? false;
            return $formValues;
        } else {
            return $this->getAttributeFormDefaultValues($attributeCode);
        }
    }

    /**
     * Get attribute form default values
     *
     * @param string $attributeCode
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getAttributeFormDefaultValues($attributeCode)
    {
        $metadata = $this->attributeMetadata->getAttributeMetadata($attributeCode);

        return [
            BooleanMetaField::IS_MOVED => false,
            BooleanMetaField::VISIBLE => $metadata->isVisible(),
            BooleanMetaField::REQUIRED => $metadata->isRequired(),
            'label' => $metadata->getFrontendLabel()
        ];
    }

    /**
     * Get new customer attribute link
     *
     * @return string
     */
    public function getNewCustomerAttributeLink()
    {
        $link = '';

        if ($this->moduleManager->isAheadworksCustomerAttributesEnabled()) {
            $link = $this->getUrl('aw_customer_attributes/attribute/new');
        }

        if ($this->moduleManager->isMagentoCustomerAttributesEnabled()) {
            $link = $this->getUrl('adminhtml/customer_attribute/new');
        }

        return $link;
    }
}
