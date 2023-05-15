<?php
declare(strict_types=1);

namespace Aheadworks\OneStepCheckout\Model;

use Aheadworks\OneStepCheckout\Model\Cart\ImageProvider;
use Aheadworks\OneStepCheckout\Model\Cart\OptionsProvider as ItemOptionsProvider;
use Aheadworks\OneStepCheckout\Model\ConfigProvider\DefaultShippingMethod;
use Aheadworks\OneStepCheckout\Model\ConfigProvider\PaymentMethodList;
use Aheadworks\OneStepCheckout\Model\DeliveryDate\ConfigProvider as DeliveryDateConfigProvider;
use Aheadworks\OneStepCheckout\Model\Newsletter\ConfigProvider as NewsletterConfigProvider;
use Aheadworks\OneStepCheckout\Model\Shipping\Config as ShippingConfig;
use Aheadworks\OneStepCheckout\Model\ThirdPartyModule\ModuleManager as ThirdPartyModuleManager;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @param CheckoutSession $checkoutSession
     * @param CustomerSession $customerSession
     * @param Config $config
     * @param PaymentMethodList $paymentMethodsProvider
     * @param NewsletterConfigProvider $subscriberConfigProvider
     * @param DeliveryDateConfigProvider $deliveryDateConfigProvider
     * @param ImageProvider $imageProvider
     * @param ItemOptionsProvider $itemOptionsProvider
     * @param DefaultShippingMethod $defaultShippingMethodProvider
     * @param UrlInterface $urlBuilder
     * @param ThirdPartyModuleManager $thirdPartyModuleManager
     * @param ShippingConfig $shippingConfig
     */
    public function __construct(
        private CheckoutSession $checkoutSession,
        private CustomerSession $customerSession,
        private Config $config,
        private PaymentMethodList $paymentMethodsProvider,
        private NewsletterConfigProvider $subscriberConfigProvider,
        private DeliveryDateConfigProvider $deliveryDateConfigProvider,
        private ImageProvider $imageProvider,
        private ItemOptionsProvider $itemOptionsProvider,
        private DefaultShippingMethod $defaultShippingMethodProvider,
        private UrlInterface $urlBuilder,
        private ThirdPartyModuleManager $thirdPartyModuleManager,
        private ShippingConfig $shippingConfig
    ) {
    }

    /**
     * Get config for checkout
     *
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getConfig(): array
    {
        $quoteId = $this->checkoutSession->getQuoteId();
        $quote = $this->checkoutSession->getQuote();
        $config = [
            'sameAsShippingFlag' => $this->config->isBillingShippingAreTheSame($quote->getStore()->getWebsiteId()),
            'addressTypeToDisplayFirst' => $this->config->getAddressTypeToDisplayFirst(),
            'paymentMethods' => $this->paymentMethodsProvider->getPaymentMethods($quoteId),
            'newsletterSubscribe' => $this->subscriberConfigProvider->getConfig(),
            'isOrderNoteEnabled' => $this->config->isOrderNoteEnabled(),
            'isMiniCartExpanded' => $this->config->isMiniCartExpanded(),
            'isCustomerInfoSectionDisplayed' => $this->config->isCustomerInfoSectionDisplayed(),
            'canDisplayFilledCustomerInfoFields' => $this->config->canDisplayFilledCustomerInfoFields(),
            'isEnabledCheckoutStatistics' => $this->config->isEnabledCheckoutStatistics(),
            'isAllowedToChangeProductOptions' => $this->config->isAllowedToChangeProductOptions(),
            'isAllowedCreateAccountAfterCheckout' => $this->config->isAllowedCreateAccountAfterCheckout()
                && !$this->customerSession->isLoggedIn(),
            'isEnabledDefaultPlaceOrderButton' => $this->config->isEnabledShowPlaceOrderButtonDefault(),
            'deliveryDate' => $this->deliveryDateConfigProvider->getConfig(),
            'editableItemOptions' => $this->itemOptionsProvider->getOptionsData($quoteId),
            'itemImageData' => $this->imageProvider->getConfigImageData($quoteId),
            'trustSeals' => [
                'isEnabled' => $this->config->isTrustSealsBlockEnabled(),
                'label' => $this->config->getTrustSealsLabel(),
                'text' => $this->config->getTrustSealsText(),
                'badges' => $this->config->getTrustSealsBadges()
            ],
            'defaultRedirectOnEmptyQuoteUrl' => $this->getDefaultRedirectOnEmptyQuoteUrl(),
            'googleAutocomplete' => [
                'apiKey' => $this->config->getGooglePlacesApiKey()
            ],
            'optionsPostUrl' => $this->urlBuilder->getUrl('onestepcheckout/index/optionspost'),
            'vatValidationUrl' => $this->urlBuilder->getUrl('onestepcheckout/index/vatvalidation'),
            'reloadAfterQuoteItemRemovalFlag' => $this->getReloadAfterQuoteItemRemovalFlag(),
        ];

        $defaultShippingMethod = $this->defaultShippingMethodProvider->getShippingMethod();
        if (!empty($defaultShippingMethod)) {
            $config['defaultShippingMethod'] = $defaultShippingMethod;
        }
        if ($this->config->getDefaultPaymentMethod()) {
            $config['defaultPaymentMethod'] = $this->config->getDefaultPaymentMethod();
        }
        if ($quote->getIsVirtual()) {
            unset($config['paymentMethods']);
        }
        return $config;
    }

    /**
     * Retrieve default redirect on empty quote page URL
     *
     * @return string
     */
    private function getDefaultRedirectOnEmptyQuoteUrl(): string
    {
        $url = $this->checkoutSession->getContinueShoppingUrl(true);
        if (!$url) {
            $url = $this->urlBuilder->getUrl();
        }
        return $url;
    }

    /**
     * Retrieve flag if need to reload checkout page after quote item removal
     *
     * @return bool
     */
    private function getReloadAfterQuoteItemRemovalFlag(): bool
    {
        return $this->thirdPartyModuleManager->isMagentoMsiInStorePickupFrontendModuleEnabled()
            && $this->shippingConfig->isMethodActive(
                ShippingConfig::INVENTORY_IN_STORE_PICKUP_CARRIER_CODE
            );
    }
}
