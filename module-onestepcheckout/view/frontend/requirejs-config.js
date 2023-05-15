var config = {
    map: {
        '*': {
            awOscSidebar:           'Aheadworks_OneStepCheckout/js/sidebar',
            awOscFloatLabel:        'Aheadworks_OneStepCheckout/js/float-label',
            awOscAsyncAccordion:    'Aheadworks_OneStepCheckout/js/async-accordion',
            awOscValidationMock:    'Aheadworks_OneStepCheckout/js/validation-mock',
            awOscSwatchRenderer:    'Aheadworks_OneStepCheckout/js/widget/swatch-renderer'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/model/checkout-data-resolver': {
                'Aheadworks_OneStepCheckout/js/model/checkout-data-resolver-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                'Aheadworks_OneStepCheckout/js/model/place-order-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'Aheadworks_OneStepCheckout/js/model/set-payment-information-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information-extended': {
                'Aheadworks_OneStepCheckout/js/action/set-payment-information-extended-mixin': true
            },
            'Magento_CheckoutAgreements/js/model/agreements-modal': {
                'Aheadworks_OneStepCheckout/js/model/checkout-agreements/modal-mixin': true
            },
            'Magento_Checkout/js/model/new-customer-address': {
                'Aheadworks_OneStepCheckout/js/model/new-customer-address-mixin': true
            },
            'Magento_CheckoutAgreements/js/model/agreements-assigner': {
                'Aheadworks_OneStepCheckout/js/model/checkout-agreements/assigner-mixin': true
            },
            'Magento_Checkout/js/view/summary/abstract-total': {
                'Aheadworks_OneStepCheckout/js/view/totals/abstract-total-mixin': true
            },
            'Magento_Checkout/js/model/quote': {
                'Aheadworks_OneStepCheckout/js/model/quote-mixin': true
            },
            'Magento_Ui/js/form/element/abstract': {
                'Aheadworks_OneStepCheckout/js/view/form/element/abstract-mixin': true
            },
            'Magento_Checkout/js/action/select-billing-address': {
                'Aheadworks_OneStepCheckout/js/model/select-billing-address-mixin': true
            },
            'Magento_Braintree/js/view/payment/method-renderer/hosted-fields': {
                'Aheadworks_OneStepCheckout/js/view/payment-method/renderer/braintree/hosted-fields-mixin': true
            },
            'Magento_Braintree/js/view/payment/method-renderer/paypal': {
                'Aheadworks_OneStepCheckout/js/view/payment-method/renderer/braintree/paypal-mixin': true
            },
            'PayPal_Braintree/js/view/payment/method-renderer/hosted-fields': {
                'Aheadworks_OneStepCheckout/js/view/payment-method/renderer/braintree/hosted-fields-mixin': true
            },
            'PayPal_Braintree/js/view/payment/method-renderer/paypal': {
                'Aheadworks_OneStepCheckout/js/view/payment-method/renderer/braintree/paypal-mixin': true
            },
            'PayPal_Braintree/js/googlepay/implementations/core-checkout/method-renderer/googlepay': {
                'Aheadworks_OneStepCheckout/js/view/payment-method/renderer/braintree/googlepay-mixin': true
            },
            'Magento_Braintree/js/googlepay/implementations/core-checkout/method-renderer/googlepay': {
                'Aheadworks_OneStepCheckout/js/view/payment-method/renderer/braintree/googlepay-mixin': true
            },
            'PayPal_Braintree/js/applepay/implementations/core-checkout/method-renderer/applepay': {
                'Aheadworks_OneStepCheckout/js/view/payment-method/renderer/braintree/applepay-mixin': true
            },
            'Magento_Braintree/js/applepay/implementations/core-checkout/method-renderer/applepay': {
                'Aheadworks_OneStepCheckout/js/view/payment-method/renderer/braintree/applepay-mixin': true
            },
            'Magento_Checkout/js/action/get-payment-information': {
                'Aheadworks_OneStepCheckout/js/action/get-payment-information-mixin': true
            },
            'Magento_Paypal/js/view/payment/method-renderer/in-context/checkout-express': {
                'Aheadworks_OneStepCheckout/js/model/checkout-express-mixin': true
            },
            'Aheadworks_Nmi/js/view/payment/method-renderer/hosted-fields': {
                'Aheadworks_OneStepCheckout/js/view/payment-method/renderer/aw-nmi/hosted-fields-mixin': true
            },
            'Magento_Paypal/js/view/payment/method-renderer/payflowpro-method': {
                'Aheadworks_OneStepCheckout/js/view/payment-method/renderer/paypal/payflowpro-method-mixin': true
            },
            'Magento_Checkout/js/model/shipping-rates-validator': {
                'Aheadworks_OneStepCheckout/js/model/shipping-rates-validator-mixin': true
            },
            'Magento_Checkout/js/model/address-converter': {
                'Aheadworks_OneStepCheckout/js/model/address-converter-mixin': true
            },
            'Magento_Checkout/js/action/select-shipping-address': {
                'Aheadworks_OneStepCheckout/js/action/select-shipping-address-mixin': true
            },
            'Magento_Checkout/js/model/error-processor': {
                'Aheadworks_OneStepCheckout/js/model/error-processor-mixin': true
            },
            'Magento_InventoryInStorePickupFrontend/js/model/pickup-locations-service': {
                'Aheadworks_OneStepCheckout/js/model/inventory/instore-pickup/pickup-locations-service-mixin': true
            },
            'Magento_GiftWrapping/js/model/gift-wrapping': {
                'Aheadworks_OneStepCheckout/js/model/sidebar/gift-wrapping-mixin': true
            },
            'Adyen_Payment/js/view/payment/method-renderer/adyen-hpp-method': {
                'Aheadworks_OneStepCheckout/js/view/payment-method/renderer/adyen/adyen-hpp-method-mixin': true
            },
            'Adyen_Payment/js/view/payment/method-renderer/adyen-pos-cloud-method': {
                'Aheadworks_OneStepCheckout/js/view/payment-method/renderer/adyen/adyen-pos-cloud-method-mixin': true
            },
            'Magento_Paypal/js/in-context/express-checkout-smart-buttons': {
                'Aheadworks_OneStepCheckout/js/view/payment-method/renderer/paypal-express/express-checkout-smart-buttons-mixin': true
            },
            'Magento_PaymentServicesPaypal/js/view/payment/method-renderer/hosted-fields': {
                'Aheadworks_OneStepCheckout/js/view/payment-method/renderer/payment-services-paypal/hosted-fields-mixin': true
            },
            'Magento_PaymentServicesPaypal/js/view/payment/method-renderer/vault': {
                'Aheadworks_OneStepCheckout/js/view/payment-method/renderer/payment-services-paypal/vault-mixin': true
            },
        }
    }
};
