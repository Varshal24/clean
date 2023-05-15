<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\Address;

use Aheadworks\OneStepCheckout\Model\Config\Source\Customization\CustomValidation;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Helper\Address as AddressHelper;
use Magento\Store\Model\ScopeInterface;
use Aheadworks\OneStepCheckout\Model\Address\Attribute\Code\Resolver as AddressAttributeCodeResolver;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\ModuleManager;
use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\AvailabilityChecker;
use Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow\DefaultFieldMoving;
use Aheadworks\OneStepCheckout\Model\Address\Form\Customization\Config;
use Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow\DefaultSortOrder;
use Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\BaseCustomization;
use Aheadworks\OneStepCheckout\Model\Attribute\Form\AttributeMeta\Provider as AttributeMetadataProvider;
use Aheadworks\OneStepCheckout\Model\Config\Source\Customization\BooleanMetaField;

/**
 * Address block for form customization
 */
class Customization extends BaseCustomization
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'system/config/address/customization.phtml';

    /**
     * @param Context $context
     * @param AttributeMetadataProvider $attributeMetadata
     * @param AvailabilityChecker $availabilityChecker
     * @param Config $customizationConfig
     * @param DefaultSortOrder $defaultSortOrder
     * @param AddressAttributeCodeResolver $addressAttributeCodeResolver
     * @param DefaultFieldMoving $defaultFieldMoving
     * @param ModuleManager $moduleManager
     * @param BooleanMetaField $booleanMetaField
     * @param CustomValidation $customValidation
     * @param string $addressType
     * @param array $data
     */
    public function __construct(
        protected Context $context,
        protected AttributeMetadataProvider $attributeMetadata,
        private AvailabilityChecker $availabilityChecker,
        private Config $customizationConfig,
        protected DefaultSortOrder $defaultSortOrder,
        private AddressAttributeCodeResolver $addressAttributeCodeResolver,
        private DefaultFieldMoving $defaultFieldMoving,
        protected ModuleManager $moduleManager,
        protected BooleanMetaField $booleanMetaField,
        protected CustomValidation $customValidation,
        private string $addressType = 'default',
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
        $attributes = $this->attributeMetadata->getAttributeMetadataList('customer_register_address');
        foreach ($attributes as $attributesMeta) {
            if (!$this->availabilityChecker->isAvailableOnForm($attributesMeta)) {
                continue;
            }
            $attributeCode = $attributesMeta->getAttributeCode();
            $duplicatedAttrCode = $this->addressAttributeCodeResolver->getDuplicatedAttributeCode($attributeCode);
            if ($duplicatedAttrCode && array_key_exists($duplicatedAttrCode, $data)) {
                continue;
            }

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
     * @param array $defaultValues
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getAttributeFormValues($attributeCode, $defaultValues)
    {
        $value = $this->getElement()->getValue();
        if (isset($value['attributes'][$attributeCode])) {
            $formValues = $value['attributes'][$attributeCode];
            if ($this->isMultiline($attributeCode)) {
                $lineCount = $this->getMultilineCount($attributeCode);
                for ($line = 0; $line < $lineCount; $line ++) {
                    if (!isset($formValues[$line])) {
                        $formValues[$line] = $defaultValues[$line];
                    }

                    $formValues[$line][BooleanMetaField::IS_MOVED] =
                        $formValues[$line][BooleanMetaField::IS_MOVED]
                        ?? $this->defaultFieldMoving->get($attributeCode, $line);
                }

                if (!array_key_exists(0, $formValues)) {
                    $formValues = [$formValues];
                }
                if (count($formValues) != $lineCount) {
                    $defaultValues = $this->getAttributeFormDefaultValues($attributeCode);
                    $formValues = array_replace($defaultValues, $formValues);
                }
            } else {
                if (array_key_exists(0, $formValues)) {
                    $formValues = $formValues[0];
                }
            }
            $formValues[BooleanMetaField::IS_MOVED] =
                $formValues[BooleanMetaField::IS_MOVED]
                ?? $this->defaultFieldMoving->get($attributeCode);
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
        $defaultValues = [];
        $metadata = $this->attributeMetadata->getAttributeMetadata($attributeCode);
        $label = $metadata->getFrontendLabel();
        if ($this->isMultiline($attributeCode)) {
            $lineCount = $this->getMultilineCount($attributeCode);
            for ($line = 0; $line < $lineCount; $line ++) {
                $isFirstLine = ($line == 0);
                $defaultValues[$line] = [
                    BooleanMetaField::VISIBLE => true,
                    BooleanMetaField::IS_MOVED => $this->defaultFieldMoving->get($attributeCode, $line),
                    BooleanMetaField::REQUIRED => $isFirstLine,
                    'label' => $isFirstLine ? $label . ' Line' : $label . ' Line ' . ($line + 1)
                ];
            }
        } else {
            $isVisible = $metadata->isVisible();
            if ($attributeCode == 'vat_id') {
                $isVisible = $this->_scopeConfig->isSetFlag(
                    AddressHelper::XML_PATH_VAT_FRONTEND_VISIBILITY,
                    ScopeInterface::SCOPE_STORE
                );
            }
            $defaultValues = [
                BooleanMetaField::IS_MOVED => $this->defaultFieldMoving->get($attributeCode),
                BooleanMetaField::VISIBLE => $isVisible,
                BooleanMetaField::REQUIRED => $metadata->isRequired(),
                'label' => $label
            ];
        }
        return $defaultValues;
    }

    /**
     * Check if attribute is multiline
     *
     * @param string $attributeCode
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function isMultiline($attributeCode)
    {
        return $this->attributeMetadata->isMultiline($attributeCode);
    }

    /**
     * Get multiline count
     *
     * @param string $attributeCode
     * @return int
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getMultilineCount($attributeCode)
    {
        return $this->attributeMetadata->getMultilineCount($attributeCode);
    }

    /**
     * Check if modification of metadata field is allowed
     *
     * @param string $attributeCode
     * @param string $name
     * @param int|null $line
     * @return bool
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function canModifyMeta($attributeCode, $name, $line = null)
    {
        $metaEditRestrictions = $this->customizationConfig->get($this->addressType);
        if ($this->isMultiline($attributeCode)
            && $line !== null
            && isset($metaEditRestrictions[$attributeCode][$line][$name])
        ) {
            return $metaEditRestrictions[$attributeCode][$line][$name];
        } elseif (isset($metaEditRestrictions[$attributeCode][$name])) {
            return $metaEditRestrictions[$attributeCode][$name];
        }
        return true;
    }

    /**
     * Get new address attribute link
     *
     * @return string
     */
    public function getNewAddressAttributeLink()
    {
        $link = '';

        if ($this->moduleManager->isAheadworksCustomerAttributesEnabled()) {
            $link = $this->getUrl('aw_customer_attributes/address_attribute/new');
        }

        if ($this->moduleManager->isMagentoCustomerAttributesEnabled()) {
            $link = $this->getUrl('adminhtml/customer_address_attribute/new');
        }

        return $link;
    }
}
