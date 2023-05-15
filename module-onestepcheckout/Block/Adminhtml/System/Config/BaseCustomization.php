<?php
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config;

use Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow\DefaultSortOrder;
use Aheadworks\OneStepCheckout\Model\Attribute\Form\AttributeMeta\Provider as AttributeMetadataProvider;
use Aheadworks\OneStepCheckout\Model\Config\Source\Customization\BooleanMetaField;
use Aheadworks\OneStepCheckout\Model\Config\Source\Customization\CustomValidation;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\ModuleManager;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class BaseCustomization extends Field
{
    /**
     * @param Context $context
     * @param AttributeMetadataProvider $attributeMetadata
     * @param ModuleManager $moduleManager
     * @param DefaultSortOrder $defaultSortOrder
     * @param BooleanMetaField $booleanMetaDataFieldSource
     * @param CustomValidation $customValidation
     * @param array $data
     */
    public function __construct(
        protected Context $context,
        protected AttributeMetadataProvider $attributeMetadata,
        protected ModuleManager $moduleManager,
        protected DefaultSortOrder $defaultSortOrder,
        protected BooleanMetaField $booleanMetaDataFieldSource,
        protected CustomValidation $customValidation,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $this->setElement($element);
        return $this->_toHtml();
    }

    /**
     * Get field row sort order
     *
     * @param string $rowId
     * @return int|bool
     */
    public function getFieldRowSortOrder($rowId)
    {
        $value = $this->getElement()->getValue();
        return $value['rows'][$rowId]['sort_order'] ?? $this->defaultSortOrder->getRowSortOrder($rowId);
    }

    /**
     * Compare field row Ids by sort order
     *
     * @param string $rowId1
     * @param string $rowId2
     * @return int
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    protected function compareFieldRows($rowId1, $rowId2)
    {
        $row1SortOrder = $this->getFieldRowSortOrder($rowId1);
        $row2SortOrder = $this->getFieldRowSortOrder($rowId2);

        return $row1SortOrder <=> $row2SortOrder;
    }

    /**
     * Get metadata fields with boolean values
     *
     * @return array
     */
    public function getBooleanMetaFields()
    {
        return $this->booleanMetaDataFieldSource->getFields();
    }

    /**
     * Retrieve custom validation field by code
     *
     * @param string $code
     * @return array
     */
    public function getCustomValidation(string $code): array
    {
        return $this->customValidation->getByCode($code);
    }

    /**
     * Get base html Id
     *
     * @return string
     */
    public function getHtmlId()
    {
        $htmlId = $this->getData('html_id');
        if (!$htmlId) {
            $htmlId = '_' . uniqid();
            $this->setData('html_id', $htmlId);
        }
        return $htmlId;
    }

    /**
     * Get input html Id
     *
     * @param string $attributeCode
     * @param string $metaField
     * @param int|null $line
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getInputHtmlId($attributeCode, $metaField, $line = null)
    {
        $htmlId = $this->getHtmlId() . '-attribute-' . $attributeCode;
        if ($this->attributeMetadata->isMultiline($attributeCode) && $line !== null) {
            $htmlId .= '-' . $line;
        }
        $htmlId .= '-' . $metaField;
        return $htmlId;
    }

    /**
     * Get input html name
     *
     * @param string $attributeCode
     * @param string $metaField
     * @param int|null $line
     * @param string $part
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getInputHtmlName($attributeCode, $metaField, $line = null, $part = 'attributes', $prefix = null)
    {
        $htmlName = $this->getElement()->getName() . '[' . $part . '][' . $attributeCode . ']';
        if ($this->attributeMetadata->isMultiline($attributeCode) && $line !== null) {
            $htmlName .= '[' . $line . ']';
        }

        $prefix
            ? $htmlName .= '[' . $prefix . ']' . '[' . $metaField . ']'
            : $htmlName .= '[' . $metaField . ']';

        return $htmlName;
    }

    /**
     * Check if top actions block is visible
     *
     * @return bool
     */
    public function isTopActionsBlockVisible()
    {
        return $this->moduleManager->isMagentoCustomerAttributesEnabled()
            || $this->moduleManager->isAheadworksCustomerAttributesEnabled();
    }
}
