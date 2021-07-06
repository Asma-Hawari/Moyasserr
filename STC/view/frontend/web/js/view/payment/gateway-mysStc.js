/**
 * @author    Eng. Asma Hawari
 *
 * @package   Moyasser_STC
 */
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'mysStc',
                component: 'Moyasser_STC/js/view/payment/method-renderer/gateway-mysStc'
            }
        );
        return Component.extend({});
    });
