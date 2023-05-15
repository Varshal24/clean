define([
    'jquery',
    'uiComponent',
    'ko',
    'Magento_Customer/js/model/customer',
    'Magento_Customer/js/action/check-email-availability',
    'Magento_Customer/js/action/login',
    'Magento_Checkout/js/model/quote',
    'Aheadworks_OneStepCheckout/js/model/checkout-data',
    'Aheadworks_OneStepCheckout/js/model/newsletter/subscriber',
    'Aheadworks_OneStepCheckout/js/model/create-account/create-account-enabled-flag',
    'Aheadworks_OneStepCheckout/js/action/check-if-subscribed-by-email',
    'Magento_Checkout/js/model/full-screen-loader',
    'Aheadworks_OneStepCheckout/js/model/checkout-data-completeness-logger',
    'mage/validation'
], function (
    $,
    Component,
    ko,
    customer,
    checkEmailAvailabilityAction,
    loginAction,
    quote,
    checkoutData,
    newsletterSubscriber,
    createAccountEnabledFlag,
    checkIfSubscribedByEmailAction,
    fullScreenLoader,
    completenessLogger
) {
    'use strict';

    var validatedEmail;

    if (!checkoutData.getValidatedEmailValue() &&
        window.checkoutConfig.validatedEmailValue
    ) {
        checkoutData.setInputFieldEmailValue(window.checkoutConfig.validatedEmailValue);
        checkoutData.setValidatedEmailValue(window.checkoutConfig.validatedEmailValue);
    }
    validatedEmail = checkoutData.getValidatedEmailValue();

    var newsletterSubscribeConfig = window.checkoutConfig.newsletterSubscribe,
        verifiedIsSubscribed = checkoutData.getVerifiedIsSubscribedFlag(),
        loginFormSelector = 'form[data-role=email-with-possible-login]',
        userNameSelector = loginFormSelector + ' input[name=username]';

    if (validatedEmail && !customer.isLoggedIn()) {
        quote.guestEmail = validatedEmail;
        if (newsletterSubscribeConfig.isGuestSubscriptionsAllowed) {
            newsletterSubscriber.subscriberEmail = validatedEmail;
            if (verifiedIsSubscribed !== undefined) {
                newsletterSubscriber.isSubscribed(verifiedIsSubscribed);
                newsletterSubscriber.subscribedStatusVerified(true);
            }
        }
    }

    return Component.extend({
        defaults: {
            template: 'Aheadworks_OneStepCheckout/form/email',
            email: checkoutData.getInputFieldEmailValue(),
            emailFocused: false,
            isLoading: false,
            isPasswordVisible: false,
            listens: {
                email: 'emailHasChanged',
                emailFocused: 'validateEmail'
            }
        },
        isCreateAccountCheckedFlag: window.checkoutConfig.isAllowedCreateAccountAfterCheckout,
        checkDelay: 2000,
        checkAvailabilityRequest: null,
        checkIfSubscribedRequest: null,
        isCustomerLoggedIn: customer.isLoggedIn,
        forgotPasswordUrl: window.checkoutConfig.forgotPasswordUrl,
        emailCheckTimeout: 0,

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();
            createAccountEnabledFlag(
                this.isCustomerLoggedIn()
                    ? false
                    : window.checkoutConfig.isAllowedCreateAccountAfterCheckout
            );
            completenessLogger.bindField('email', this.email);
            this.emailHasChanged()
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .observe([
                    'email',
                    'emailFocused',
                    'isLoading',
                    'isPasswordVisible',
                    'isCreateAccountCheckedFlag',
                    'isAllowedCreateAccount'
                ]);

            this.isAllowedCreateAccount = ko.computed(function () {
                return window.checkoutConfig.isAllowedCreateAccountAfterCheckout && !this.isPasswordVisible()
            }, this);

            this.isCreateAccountCheckedFlag.subscribe(function (value) {
                createAccountEnabledFlag(value);
            },this);

            this.isPasswordVisible.subscribe(function (value) {
                if (value) {
                    createAccountEnabledFlag(false);
                } else {
                    createAccountEnabledFlag(this.isCreateAccountCheckedFlag());
                }
            },this);

            return this;
        },

        /**
         * Process email value change
         */
        emailHasChanged: function () {
            var self = this;

            clearTimeout(this.emailCheckTimeout);

            if (self.validateEmail()) {
                quote.guestEmail = self.email();
                newsletterSubscriber.subscriberEmail = self.email();
                checkoutData.setValidatedEmailValue(self.email());
            }
            this.emailCheckTimeout = setTimeout(function () {
                if (self.validateEmail()) {
                    self.checkEmailAvailability();
                    if (newsletterSubscribeConfig.isGuestSubscriptionsAllowed) {
                        self.checkIfSubscribedByEmail();
                    }
                } else {
                    self.isPasswordVisible(false);
                    newsletterSubscriber.subscribedStatusVerified(false);
                }
            }, self.checkDelay);

            checkoutData.setInputFieldEmailValue(self.email());
        },

        /**
         * Check email availability
         */
        checkEmailAvailability: function () {
            var self = this,
                isEmailCheckComplete = $.Deferred();

            this._validateRequest(this.checkAvailabilityRequest);
            this.isLoading(true);
            this.checkAvailabilityRequest = checkEmailAvailabilityAction(isEmailCheckComplete, this.email());

            $.when(isEmailCheckComplete).done(function () {
                self.isPasswordVisible(false);
            }).fail(function () {
                self.isPasswordVisible(true);
            }).always(function () {
                self.isLoading(false);
            });
        },

        /**
         * Check if subscribed by email
         */
        checkIfSubscribedByEmail: function () {
            var isEmailCheckComplete = $.Deferred();

            this._validateRequest(this.checkIfSubscribedRequest);
            this.checkIfSubscribedRequest = checkIfSubscribedByEmailAction(isEmailCheckComplete, this.email());

            $.when(isEmailCheckComplete).done(function () {
                newsletterSubscriber.isSubscribed(true);
                checkoutData.setVerifiedIsSubscribedFlag(true);
            }).fail(function () {
                newsletterSubscriber.isSubscribed(false);
                checkoutData.setVerifiedIsSubscribedFlag(false);
            }).always(function () {
                newsletterSubscriber.subscribedStatusVerified(true);
            });
        },

        /**
         * If request has been sent abort it
         *
         * @param {XMLHttpRequest} request
         */
        _validateRequest: function (request) {
            if (request != null && $.inArray(request.readyState, [1, 2, 3])) {
                request.abort();
                request = null;
            }
        },

        /**
         * Get login form for validation
         *
         * @return {Object}
         */
        getLoginForm: function () {
            return $(loginFormSelector);
        },

        /**
         * Check available login form
         *
         * @return {Boolean}
         */
        isLoginFormAvailable: function () {
            return !!this.getLoginForm().length;
        },

        /**
         * Validation login form
         *
         */
        validationLoginForm: function () {
            this.getLoginForm().validation();
        },

        /**
         * Local email validation
         *
         * @param {Boolean} focused
         * @returns {Boolean}
         */
        validateEmail: function (focused) {
            var validator;
            if (!this.isLoginFormAvailable()){
                return true;
            }
            this.validationLoginForm();
            if (focused === false && !!this.email()) {
                return !!$(userNameSelector).valid();
            }
            validator = this.getLoginForm().validate();

            return validator.check(userNameSelector);
        },

        /**
         *  Email validation after pushing button place order
         *
         * @returns {Boolean}
         */
        validateEmailOnPlaceOrder: function () {
            if (!this.isLoginFormAvailable()) {
                return true;
            }
            this.validationLoginForm();
            if (!this.email()) {
                $(userNameSelector).valid();
                return false;
            }
            return true;
        },

        /**
         * Perform login action
         *
         * @param {Object} loginForm
         */
        login: function (loginForm) {
            var loginData = {},
                formDataArray = $(loginForm).serializeArray();

            $.each(formDataArray, function () {
                loginData[this.name] = this.value;
            });

            if (this.isPasswordVisible()
                && $(loginForm).validation()
                && $(loginForm).validation('isValid')
            ) {
                fullScreenLoader.startLoader();
                loginAction(loginData).always(function() {
                    fullScreenLoader.stopLoader();
                });
            }
        },

        /**
         * Scroll to invalid block on form
         *
         * @returns {Object}
         */
        scrollInvalid: function () {
            var input = $('form[data-role=email-with-possible-login] input[name=username]');
            input.focus();
            $('html, body').animate({ scrollTop: 0 });
            return this;
        },
    });
});
