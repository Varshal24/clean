<?php
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\Report\CheckoutBehavior\CustomerAttributes;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\BooleanUtils;
use Magento\Customer\Api\CustomerMetadataInterface;
use Aheadworks\OneStepCheckout\Model\Customer\Form\AttributeMeta\AvailabilityChecker;
use Aheadworks\OneStepCheckout\Model\Config;

class MetaProvider
{
    /**
     * @var CustomerMetadataInterface
     */
    private $customerMetadata;

    /**
     * @var AvailabilityChecker
     */
    private $availabilityChecker;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var BooleanUtils
     */
    private $booleanUtils;

    /**
     * @param CustomerMetadataInterface $customerMetadata
     * @param AvailabilityChecker $availabilityChecker
     * @param Config $config
     * @param BooleanUtils $booleanUtils
     */
    public function __construct(
        CustomerMetadataInterface $customerMetadata,
        AvailabilityChecker $availabilityChecker,
        Config $config,
        BooleanUtils $booleanUtils
    ) {
        $this->customerMetadata = $customerMetadata;
        $this->availabilityChecker = $availabilityChecker;
        $this->config = $config;
        $this->booleanUtils = $booleanUtils;
    }

    /**
     * Get metadata
     *
     * @return array
     * @throws LocalizedException
     */
    public function getMetadata()
    {
        $metadata = [];
        $attrMetadata = $this->customerMetadata->getAttributes('customer_account_create');
        $formConfig = $this->config->getCustomerInfoFormConfig();
        foreach ($attrMetadata as $meta) {
            if (!$this->availabilityChecker->isAvailableOnForm($meta)) {
                continue;
            }
            $attributeCode = $meta->getAttributeCode();
            $customMeta = $formConfig['attributes'][$attributeCode] ?? null;

            if ($customMeta) {
                if (array_key_exists(0, $customMeta)) {
                    $customMeta = $customMeta[0];
                }
                if ($this->booleanUtils->toBoolean($customMeta['visible'])) {
                    $metadata[] = [
                        'code' => $attributeCode,
                        'label' => $customMeta['label'],
                        'required' => $this->booleanUtils->toBoolean($customMeta['required'])
                    ];
                }
            } else {
                if ($meta->isVisible()) {
                    $metadata[] = [
                        'code' => $attributeCode,
                        'label' => $meta->getStoreLabel(),
                        'required' => $meta->isRequired()
                    ];
                }
            }
        }

        return $metadata;
    }
}
