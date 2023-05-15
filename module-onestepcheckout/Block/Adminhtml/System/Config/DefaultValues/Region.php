<?php
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\DefaultValues;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Directory\Helper\Data as DirectoryHelperData;

/**
 * Region field renderer
 *
 * @method AbstractElement getElement()
 * @method AbstractElement setElement($element)
 */
class Region extends Field
{
    const COUNTRY_ID_FIELD_PATH = 'aw_osc_default_values_country_id';
    const REGION_ID_FIELD_PATH = 'aw_osc_default_values_region_id';

    /**
     * {@inheritdoc}
     */
    protected $_template = 'system/config/default_values/region.phtml';

    /**
     * @var DirectoryHelperData
     */
    private $directoryHelper;

    /**
     * @param Context $context
     * @param DirectoryHelperData $directoryHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        DirectoryHelperData $directoryHelper,
        array $data = []
    ) {
        $this->directoryHelper = $directoryHelper;
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
     * Get region ID field value
     *
     * @return AbstractElement
     */
    public function getRegionIdElement()
    {
        return $this->getElement()->getForm()->getElement(self::REGION_ID_FIELD_PATH);
    }

    /**
     * Get country ID field value
     *
     * @return AbstractElement
     */
    public function getCountryIdElement()
    {
        return $this->getElement()->getForm()->getElement(self::COUNTRY_ID_FIELD_PATH);
    }

    /**
     * Get region json
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getRegionJson()
    {
        return $this->directoryHelper->getRegionJson();
    }
}
