/*browser:true*/
/*global define*/
define(
    [
        'jquery',
        'StripeIntegration_Payments/js/view/payment/method-renderer/method',
        'stripejs',
        'domReady!'
    ],
    function (
        $,
        Component
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                self: this,
                template: 'StripeIntegration_Payments/payment/redirect_form',
                code: "checkout_card",
                customRedirect: true
            },
            redirectAfterPlaceOrder: false,

            initObservable: function()
            {
                this._super();
                var config = window.checkoutConfig.payment["stripe_payments"];
                initStripe({ apiKey: config.stripeJsKey, locale: config.stripeJsLocale });
                return this;
            },

            redirect: function(sessionId)
            {
                var self = this;

                try
                {
                    stripe.stripeJs.redirectToCheckout({ sessionId: sessionId }, self.onRedirectFailure);
                }
                catch (e)
                {
                    console.error(e);
                }
            },

            onRedirectFailure: function(result)
            {
                if (result.error)
                    alert(result.error.message);
                else
                    alert("An error has occured.");
            },

            icons: function()
            {
                return window.checkoutConfig.payment["stripe_payments"].icons;
            }
        });
    }
);
