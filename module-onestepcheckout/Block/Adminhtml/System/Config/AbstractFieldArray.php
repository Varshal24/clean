<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
    as MagentoAbstractFieldArray;
use Aheadworks\OneStepCheckout\ViewModel\Serializer as SerializerViewModel;

class AbstractFieldArray extends MagentoAbstractFieldArray
{
    /**
     * @param Context $context
     * @param SerializerViewModel $serializer
     * @param array $data
     */
    public function __construct(
        Context $context,
        SerializerViewModel $serializer,
        array $data = [],
    ) {
        parent::__construct($context, $data);
        $this->setData('serializer', $serializer);
    }
}
