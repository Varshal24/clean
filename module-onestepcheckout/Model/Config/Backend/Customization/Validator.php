<?php
namespace Aheadworks\OneStepCheckout\Model\Config\Backend\Customization;

use Aheadworks\OneStepCheckout\Model\Config\Backend\Customization;
use Magento\Framework\Validator\AbstractValidator;

/**
 * Class Validator
 * @package Aheadworks\OneStepCheckout\Model\Config\Backend\Customization
 */
class Validator extends AbstractValidator
{
    /**
     * Returns true if and only if value meets the validation requirements
     *
     * @param Customization $entity
     * @return bool
     */
    public function isValid($entity)
    {
        $this->_clearMessages();

        $value = $entity->getValue();
        foreach ($value['attributes'] as $attributeConfig) {
            if (isset($attributeConfig['label'])) {
                if (empty($attributeConfig['label'])) {
                    $this->_addMessages(['Label is required.']);
                }
            } else {
                foreach ($attributeConfig as $attrLineConfig) {
                    if (is_array($attrLineConfig) && empty($attrLineConfig['label'])) {
                        $this->_addMessages(['Label is required.']);
                    }
                }
            }
        }

        return empty($this->getMessages());
    }
}
