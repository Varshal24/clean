<?php
namespace Aheadworks\OneStepCheckout\Model;

use Aheadworks\OneStepCheckout\Model\Address\Form\Customization\Config\Reader;
use Aheadworks\OneStepCheckout\Model\Address\Form\FieldRow\DefaultFieldMoving;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Helper\Address as AddressHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Directory\Helper\Data;

/**
 * Main module configuration
 */
class Config
{
    /**
     * Configuration path to checkout enabled
     */
    const XML_PATH_CHECKOUT_ENABLED = 'aw_osc/general/enabled';

    /**
     * Configuration path to checkout title
     */
    const XML_PATH_CHECKOUT_TITLE = 'aw_osc/general/title';

    /**
     * Configuration path to checkout description
     */
    const XML_PATH_CHECKOUT_DESCRIPTION = 'aw_osc/general/description';

    /**
     * Configuration path to apply discount code enable flag
     */
    const XML_PATH_APPLY_DISCOUNT_CODE_ENABLE = 'aw_osc/general/apply_discount_code';

    /**
     * Configuration path to order note enable flag
     */
    const XML_PATH_APPLY_ORDER_NOTE_ENABLE = 'aw_osc/general/order_note_enabled';

    /**
     * Configuration path to Google Places API key
     */
    const XML_PATH_GOOGLE_AUTO_COMPLETE_ENABLED = 'aw_osc/general/google_autocomplete_enabled';

    /**
     * Configuration path to Google autocomplete enabled flag
     */
    const XML_PATH_GOOGLE_PLACES_API_KEY = 'aw_osc/general/google_places_api_key';

    /**
     * Configuration path to expand mini-cart by default flag
     */
    const XML_PATH_MINI_CART_EXPANDED = 'aw_osc/general/mini_cart_expanded';

    /**
     * Configuration path to enable GC and coupon code field merge
     */
    const XML_PATH_ENABLE_GC_AND_COUPON_CODE_FIELD_MERGE = 'aw_osc/general/enable_gc_and_coupon_code_field_merge';

    /**
     * Configuration path to enable display place order button near payment methods
     */
    const XML_PATH_SHOW_PLACE_ORDER_BUTTON_DEFAULT = 'aw_osc/general/show_place_order_button_default';

    /**
     * Configuration path to expand mini-cart by default flag
     */
    const XML_PATH_DISPLAY_TOP_MENU = 'aw_osc/general/display_top_menu';

    /**
     * Configuration path to enable checkout statistics by default flag
     */
    const XML_PATH_ENABLE_CHECKOUT_STATISTICS = 'aw_osc/general/enable_checkout_statistics';

    /**
     * Configuration path to allow changing product options flag
     */
    const XML_PATH_IS_ALLOWED_TO_CHANGE_PRODUCT_OPTIONS = 'aw_osc/general/is_allowed_to_change_product_options';

    /**
     * Configuration path to allow create customer account flag
     */
    const XML_PATH_IS_ALLOWED_TO_CREATE_ACCOUNT_AFTER_CHECKOUT = 'aw_osc/general/is_allowed_to_create_account_after_checkout';

    /**
     * Configuration path to newsletter subscribe option enable flag
     */
    const XML_PATH_NEWSLETTER_SUBSCRIBE_ENABLE = 'aw_osc/newsletter/enable';

    /**
     * Configuration path to newsletter subscribe checked by default
     */
    const XML_PATH_NEWSLETTER_SUBSCRIBE_CHECKED = 'aw_osc/newsletter/checked_by_default';

    /**
     * Configuration path to default value of country
     */
    const XML_PATH_DEFAULT_COUNTRY_ID = 'aw_osc/default_values/country_id';

    /**
     * Configuration path to default value of region ID
     */
    const XML_PATH_DEFAULT_REGION_ID = 'aw_osc/default_values/region_id';

    /**
     * Configuration path to default value of region
     */
    const XML_PATH_DEFAULT_REGION = 'aw_osc/default_values/region';

    /**
     * Configuration path to default value of city
     */
    const XML_PATH_DEFAULT_CITY = 'aw_osc/default_values/city';

    /**
     * Configuration path to default shipping method
     */
    const XML_PATH_DEFAULT_SHIPPING_METHOD = 'aw_osc/default_values/shipping_method';

    /**
     * Configuration path to default payment method
     */
    const XML_PATH_DEFAULT_PAYMENT_METHOD = 'aw_osc/default_values/payment_method';

    /**
     * Configuration path to delivery date display option
     */
    const XML_PATH_DELIVERY_DATE_DISPLAY_OPTION = 'aw_osc/delivery_date/display_option';

    /**
     * Configuration path to delivery date available weekdays
     */
    const XML_PATH_DELIVERY_DATE_AVAILABLE_WEEKDAYS = 'aw_osc/delivery_date/available_weekdays';

    /**
     * Configuration path to delivery date available time slots
     */
    const XML_PATH_DELIVERY_DATE_AVAILABLE_TIME_SLOTS = 'aw_osc/delivery_date/available_time_slots';

    /**
     * Configuration path to non delivery periods
     */
    const XML_PATH_DELIVERY_DATE_NON_DELIVERY_PERIODS = 'aw_osc/delivery_date/non_delivery_periods';

    /**
     * Configuration path to minimal order delivery period
     */
    const XML_PATH_DELIVERY_DATE_MIN_ORDER_DELIVERY_PERIOD = 'aw_osc/delivery_date/min_order_delivery_period';

    /**
     * Configuration path to Same day delivery is unavailable after
     */
    const XML_PATH_SAME_DAY_DELIVERY_UNAVAILABLE_AFTER = 'aw_osc/delivery_date/same_day_delivery_unavailable_after';

    /**
     * Configuration path to Next day delivery is unavailable after
     */
    const XML_PATH_NEXT_DAY_DELIVERY_UNAVAILABLE_AFTER = 'aw_osc/delivery_date/next_day_delivery_unavailable_after';

    /**
     * Configuration path to estimated delivery date
     */
    const XML_PATH_ESTIMATED_DELIVERY_DATE = 'aw_osc/delivery_date/estimated_delivery_date';

    /**
     * Configuration path to estimated delivery date
     */
    const XML_PATH_DELIVERY_DATE_NOTE_IS_ENABLED = 'aw_osc/delivery_date/note_is_enabled';

    /**
     * Configuration path to estimated delivery date
     */
    const XML_PATH_DELIVERY_DATE_NOTE = 'aw_osc/delivery_date/note';

    /**
     * Configuration path to customer info fields customization config
     */
    const XML_PATH_CUSTOMER_INFO_FORM_CUSTOMIZATION = 'aw_osc/customer_info_customization/fields_customization';

    /**
     * Configuration path to customer info display customer info section config
     */
    const XML_PATH_CUSTOMER_INFO_DISPLAY_SECTION = 'aw_osc/customer_info_customization/display_customer_info_section';

    /**
     * Configuration path to customer info display filled fields config
     */
    const XML_PATH_CUSTOMER_INFO_DISPLAY_FILLED_FIELDS = 'aw_osc/customer_info_customization/display_filled_fields';

    /**
     * Configuration path to billing fields customization config
     */
    const XML_PATH_BILLING_ADDRESS_FORM_CUSTOMIZATION = 'aw_osc/billing_customization/fields_customization';

    /**
     * Configuration path to billing and shipping addresses are the same by option
     */
    const XML_PATH_BILLING_SHIPPING_ARE_THE_SAME = 'aw_osc/addresses_settings/billing_shipping_are_the_same';

    /**
     * Configuration path to address type display first config
     */
    const XML_PATH_ADDRESS_TYPE_TO_DISPLAY_FIRST = 'aw_osc/addresses_settings/address_type_to_display_first';

    /**
     * Configuration path to shipping fields customization config
     */
    const XML_PATH_SHIPPING_ADDRESS_FORM_CUSTOMIZATION = 'aw_osc/shipping_customization/fields_customization';

    /**
     * Configuration path to trust seals block enabled flag
     */
    const XML_PATH_TRUST_SEALS_ENABLED = 'aw_osc/trust_seals/enabled';

    /**
     * Configuration path to trust seals block label
     */
    const XML_PATH_TRUST_SEALS_LABEL = 'aw_osc/trust_seals/label';

    /**
     * Configuration path to trust seals block text
     */
    const XML_PATH_TRUST_SEALS_TEXT = 'aw_osc/trust_seals/text';

    /**
     * Configuration path to trust seals block badges
     */
    const XML_PATH_TRUST_SEALS_BADGES = 'aw_osc/trust_seals/badges';

    /**
     * Configuration path to geo ip detection enabled flag
     */
    const XML_PATH_GEO_IP_ENABLED = 'aw_osc/geo_ip/enabled';

    /**
     * Configuration path to geo ip detection license key
     */
    const XML_PATH_GEO_IP_LICENSE_KEY = 'aw_osc/geo_ip/license_key';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var DefaultFieldMoving
     */
    private $defaultFieldMoving;

    /**
     * @var AddressMetadataInterface
     */
    private $addressMetadata;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer,
        AddressMetadataInterface $addressMetadata,
        Reader $reader,
        DefaultFieldMoving $defaultFieldMoving
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->defaultFieldMoving = $defaultFieldMoving;
        $this->addressMetadata = $addressMetadata;
        $this->reader = $reader;
    }

    /**
     * Check if apply discount code enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CHECKOUT_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get checkout page title
     *
     * @return string
     */
    public function getCheckoutTitle()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CHECKOUT_TITLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get checkout page description
     *
     * @return string
     */
    public function getCheckoutDescription()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CHECKOUT_DESCRIPTION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if apply discount code enabled
     *
     * @return bool
     */
    public function isApplyDiscountCodeEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_APPLY_DISCOUNT_CODE_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if order note enabled
     *
     * @return bool
     */
    public function isOrderNoteEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_APPLY_ORDER_NOTE_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if mini cart is expanded by default
     *
     * @return bool
     */
    public function isMiniCartExpanded()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_MINI_CART_EXPANDED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if display top menu
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isDisplayTopMenu($websiteId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DISPLAY_TOP_MENU,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Check if check out reporting is enabled
     *
     * @return bool
     */
    public function isEnabledCheckoutStatistics()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE_CHECKOUT_STATISTICS);
    }

    /**
     * Check if enabled GC and coupon code field merge
     *
     * @return bool
     */
    public function isEnabledGcAndCouponCodeFieldMerge()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLE_GC_AND_COUPON_CODE_FIELD_MERGE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if enabled show place order button default
     *
     * @param int $storeId
     * @return bool
     */
    public function isEnabledShowPlaceOrderButtonDefault($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_SHOW_PLACE_ORDER_BUTTON_DEFAULT,
            ScopeInterface::SCOPE_STORE,
            $storeId = null
        );
    }

    /**
     * Check if product options can be changed on checkout
     *
     * @param int $storeId
     * @return bool
     */
    public function isAllowedToChangeProductOptions($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_IS_ALLOWED_TO_CHANGE_PRODUCT_OPTIONS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if allowed create customer account after checkout
     *
     * @param int $storeId
     * @return bool
     */
    public function isAllowedCreateAccountAfterCheckout($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_IS_ALLOWED_TO_CREATE_ACCOUNT_AFTER_CHECKOUT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if newsletter subscribe option enabled
     *
     * @return bool
     */
    public function isNewsletterSubscribeOptionEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_NEWSLETTER_SUBSCRIBE_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if newsletter subscribe option checked by default
     *
     * @return bool
     */
    public function isNewsletterSubscribeOptionCheckedByDefault()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_NEWSLETTER_SUBSCRIBE_CHECKED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get default country ID
     *
     * @return string|null
     */
    public function getDefaultCountryId()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_COUNTRY_ID,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get default region ID
     *
     * @return int|string|null
     */
    public function getDefaultRegionId()
    {
        if (!$this->getDefaultCountryId()) {
            return null;
        }
        return $this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_REGION_ID,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get default region
     *
     * @param int|null $storeId
     * @return int|string|null
     */
    public function getDefaultRegion($storeId = null)
    {
        if (!$this->getDefaultCountryId()) {
            return null;
        }

        return $this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_REGION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get default city
     *
     * @return string|null
     */
    public function getDefaultCity()
    {
        if (!$this->getDefaultCountryId()) {
            return null;
        }
        return $this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_CITY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get default shipping method
     *
     * @return string
     */
    public function getDefaultShippingMethod()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_SHIPPING_METHOD,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get default payment method
     *
     * @return string
     */
    public function getDefaultPaymentMethod()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_PAYMENT_METHOD,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get delivery date display option
     *
     * @return int
     */
    public function getDeliveryDateDisplayOption()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DELIVERY_DATE_DISPLAY_OPTION,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get weekdays available for delivery
     *
     * @return array
     */
    public function getDeliveryDateAvailableWeekdays()
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_DELIVERY_DATE_AVAILABLE_WEEKDAYS,
            ScopeInterface::SCOPE_STORE
        );
        return (empty($value)) ? [] : explode(',', $value);
    }

    /**
     * Get time slots available for delivery
     *
     * @return array
     */
    public function getDeliveryDateTimeSlots()
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_DELIVERY_DATE_AVAILABLE_TIME_SLOTS,
            ScopeInterface::SCOPE_STORE
        );
        return $value ? $this->serializer->unserialize($value) : [];
    }

    /**
     * Get non delivery periods
     *
     * @return array
     */
    public function getNonDeliveryPeriods()
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_DELIVERY_DATE_NON_DELIVERY_PERIODS,
            ScopeInterface::SCOPE_STORE
        );
        return $value ? $this->serializer->unserialize($value) : [];
    }

    /**
     * Get minimal order delivery period
     *
     * @return int
     */
    public function getMinOrderDeliveryPeriod()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_DELIVERY_DATE_MIN_ORDER_DELIVERY_PERIOD,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Same Day Delivery unavailable after
     *
     * @return string
     */
    public function getSameDayDeliveryUnavailableAfter(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_SAME_DAY_DELIVERY_UNAVAILABLE_AFTER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Next Day Delivery unavailable after
     *
     * @return string
     */
    public function getNextDayDeliveryUnavailableAfter(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_NEXT_DAY_DELIVERY_UNAVAILABLE_AFTER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Estimated Delivery Date
     *
     * @return array
     */
    public function getEstimatedDeliveryDate(): array
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_ESTIMATED_DELIVERY_DATE,
            ScopeInterface::SCOPE_STORE
        );
        return $value ? $this->serializer->unserialize($value) : [];
    }

    /**
     * Check if delivery date note enabled
     *
     * @return bool
     */
    public function isDeliveryDateNoteEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_DELIVERY_DATE_NOTE_IS_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get delivery date note
     *
     * @return string
     */
    public function getDeliveryDateNote(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::XML_PATH_DELIVERY_DATE_NOTE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get minimal order delivery period
     *
     * @param int|null $websiteId
     * @return string
     */
    public function getTimezone(int $websiteId = null): string
    {
        return $this->scopeConfig->getValue(
            Data::XML_PATH_DEFAULT_TIMEZONE,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Check if customer info section is displayed
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isCustomerInfoSectionDisplayed($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CUSTOMER_INFO_DISPLAY_SECTION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if customer filled fields are required to display
     *
     * @param int|null $storeId
     * @return bool
     */
    public function canDisplayFilledCustomerInfoFields($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CUSTOMER_INFO_DISPLAY_FILLED_FIELDS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get customer info form config
     *
     * @param int|null $storeId
     * @return array
     */
    public function getCustomerInfoFormConfig($storeId = null)
    {
        $config = [];
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_CUSTOMER_INFO_FORM_CUSTOMIZATION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if ($value) {
            $config = $this->serializer->unserialize($value);
        }

        return $config;
    }

    /**
     * Get address form config
     *
     * @param string $addressType
     * @return array
     */
    public function getAddressFormConfig($addressType)
    {
        $config = [];
        $path = $addressType == 'billing'
            ? self::XML_PATH_BILLING_ADDRESS_FORM_CUSTOMIZATION
            : self::XML_PATH_SHIPPING_ADDRESS_FORM_CUSTOMIZATION;
        $value = $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
        if ($value) {
            $config = $this->serializer->unserialize($value);
        } else {
            $defaultSettings = [];
            $fields = $this->reader->read();
            foreach (array_keys($fields[$addressType]) as $field) {
                $defaultSettings[$field] = $this->getAttributeFormDefaultValues($field);
            }
            $config['attributes'] = $defaultSettings;
        }
        return $config;
    }

    /**
     * Check if billing and shipping are the same
     *
     * @param int|null $websiteId
     * @return bool
     */
    public function isBillingShippingAreTheSame($websiteId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_BILLING_SHIPPING_ARE_THE_SAME,
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Get address type to display first param
     *
     * @return string
     */
    public function getAddressTypeToDisplayFirst(): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ADDRESS_TYPE_TO_DISPLAY_FIRST,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if trust seals block enabled
     *
     * @return bool
     */
    public function isTrustSealsBlockEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_TRUST_SEALS_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get trust seals block label
     *
     * @return string
     */
    public function getTrustSealsLabel()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TRUST_SEALS_LABEL,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get trust seals block text
     *
     * @return string
     */
    public function getTrustSealsText()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TRUST_SEALS_TEXT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get trust seals badges
     *
     * @return array
     */
    public function getTrustSealsBadges()
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_TRUST_SEALS_BADGES,
            ScopeInterface::SCOPE_STORE
        );
        return $value ? $this->serializer->unserialize($value) : [];
    }

    /**
     * Check if geo ip detection enabled
     *
     * @return bool
     */
    public function isGeoIpDetectionEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GEO_IP_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get geoIp license key
     *
     * @return bool
     */
    public function getLicenseKey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GEO_IP_LICENSE_KEY,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if Google autocomplete enabled
     *
     * @return bool
     */
    public function isGoogleAutoCompleteEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_GOOGLE_AUTO_COMPLETE_ENABLED,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * Get Google Places API key
     *
     * @return string
     */
    public function getGooglePlacesApiKey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GOOGLE_PLACES_API_KEY,
            ScopeInterface::SCOPE_WEBSITE
        );
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
        $metadata = $this->addressMetadata->getAttributeMetadata($attributeCode);
        $label = $metadata->getFrontendLabel();
        $lineCount = $this->addressMetadata->getAttributeMetadata($attributeCode)->getMultilineCount();
        if ($lineCount) {
            for ($line = 0; $line < $lineCount; $line ++) {
                $isFirstLine = ($line == 0);
                $defaultValues[$line] = [
                    'visible' => true,
                    'is_moved' => $this->defaultFieldMoving->get($attributeCode, $line),
                    'required' => $isFirstLine,
                    'label' => $isFirstLine ? $label . ' Line' : $label . ' Line ' . ($line + 1)
                ];
            }
        } else {
            $isVisible = $metadata->isVisible();
            if ($attributeCode == 'vat_id') {
                $isVisible = $this->scopeConfig->isSetFlag(
                    AddressHelper::XML_PATH_VAT_FRONTEND_VISIBILITY,
                    ScopeInterface::SCOPE_STORE
                );
            }
            $defaultValues = [
                'is_moved' => $this->defaultFieldMoving->get($attributeCode),
                'visible' => $isVisible,
                'required' => $metadata->isRequired(),
                'label' => $label
            ];
        }
        return $defaultValues;
    }
}
