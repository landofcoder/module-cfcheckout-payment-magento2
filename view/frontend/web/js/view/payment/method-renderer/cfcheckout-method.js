define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Lof_Cfcheckout/js/action/set-payment-method',
    ],
    function(Component,setPaymentMethod){
    'use strict';

    return Component.extend({
        defaults:{
            'template':'Lof_Cfcheckout/payment/cfcheckout'
        },
        redirectAfterPlaceOrder: false,
        
        afterPlaceOrder: function () {
            setPaymentMethod();    
        }

    });
});
