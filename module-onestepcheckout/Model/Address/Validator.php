<?php
namespace Aheadworks\OneStepCheckout\Model\Address;

use Magento\Customer\Model\Address\AbstractAddress;
use Magento\Customer\Model\Address\ValidatorInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Directory\Helper\Data as DirectoryData;
use Aheadworks\OneStepCheckout\Model\Config as ModuleConfig;
use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Validator
 *
 * @package Aheadworks\OneStepCheckout\Model\Address
 */
class Validator implements ValidatorInterface
{
    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var DirectoryData
     */
    private $directoryData;

    /**
     * @var ModuleConfig
     */
    private $config;

    /**
     * @param EavConfig $eavConfig
     * @param DirectoryData $directoryData
     * @param ModuleConfig $config
     */
    public function __construct(
        EavConfig $eavConfig,
        DirectoryData $directoryData,
        ModuleConfig $config
    ) {
        $this->eavConfig = $eavConfig;
        $this->directoryData = $directoryData;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    public function validate(AbstractAddress $address)
    {
        $errors = array_merge(
            $this->checkRequiredFields($address),
            $this->checkOptionalFields($address)
        );

        return $errors;
    }

    /**
     * Check fields that are generally required.
     *
     * @param QuoteAddress|AbstractAddress $address
     * @return array
     * @throws \Zend_Validate_Exception
     */
    private function checkRequiredFields(AbstractAddress $address)
    {
        $errors = [];
        if (empty($address->getFirstname())) {
            $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'firstname']);
        }

        if (empty($address->getLastname())) {
            $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'lastname']);
        }

        if (empty($address->getStreetLine(1))) {
            $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'street']);
        }

        if (empty($address->getCity())) {
            $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'city']);
        }

        return $errors;
    }

    /**
     * Check fields that are conditionally required.
     *
     * @param QuoteAddress|AbstractAddress $address
     * @return array
     * @throws LocalizedException
     * @throws \Zend_Validate_Exception
     */
    private function checkOptionalFields(AbstractAddress $address)
    {
        $errors = [];
        if ($this->isTelephoneRequired($address)
            && empty($address->getTelephone())
        ) {
            $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'telephone']);
        }

        if ($this->isFaxRequired($address)
            && empty($address->getFax())
        ) {
            $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'fax']);
        }

        if ($this->isCompanyRequired($address)
            && empty($address->getCompany())
        ) {
            $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'company']);
        }

        $havingOptionalZip = $this->directoryData->getCountriesWithOptionalZip();
        if (!in_array($address->getCountryId(), $havingOptionalZip)
            && empty($address->getPostcode())
        ) {
            $errors[] = __('"%fieldName" is required. Enter and try again.', ['fieldName' => 'postcode']);
        }

        return $errors;
    }

    /**
     * Check if company field required in configuration.
     *
     * @param QuoteAddress $address
     * @return bool
     * @throws LocalizedException
     */
    private function isCompanyRequired($address)
    {
        $isRequired = $this->getAwOscAttributeIsRequired($address->getAddressType(), AddressInterface::COMPANY);
        return $isRequired
            ?? $this->eavConfig->getAttribute('customer_address', AddressInterface::COMPANY)->getIsRequired();
    }

    /**
     * Check if telephone field required in configuration.
     *
     * @param QuoteAddress $address
     * @return bool
     * @throws LocalizedException
     */
    private function isTelephoneRequired($address)
    {
        $isRequired = $this->getAwOscAttributeIsRequired($address->getAddressType(), AddressInterface::TELEPHONE);
        return $isRequired
            ?? $this->eavConfig->getAttribute('customer_address', AddressInterface::TELEPHONE)->getIsRequired();
    }

    /**
     * Check if fax field required in configuration.
     *
     * @param QuoteAddress $address
     * @return bool
     * @throws LocalizedException
     */
    private function isFaxRequired($address)
    {
        $isRequired = $this->getAwOscAttributeIsRequired($address->getAddressType(), AddressInterface::FAX);
        return $isRequired
            ?? $this->eavConfig->getAttribute('customer_address', AddressInterface::FAX)->getIsRequired();
    }

    /**
     * Get OSC form attribute value
     *
     * @param string $addressType
     * @param string $attributeCode
     * @return null|bool
     */
    private function getAwOscAttributeIsRequired($addressType, $attributeCode)
    {
        $formConfig = $this->config->getAddressFormConfig($addressType);
        if ($formConfig && isset($formConfig['attributes'][$attributeCode])) {
            $attributeConfig = $formConfig['attributes'][$attributeCode];
            return $attributeConfig[AttributeMetadataInterface::VISIBLE]
                && $attributeConfig[AttributeMetadataInterface::REQUIRED];
        }

        return null;
    }
}
