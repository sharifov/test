<?php

namespace sales\rbac\roles;

class NewDataPermissions
{
    public const PERMISSIONS = [
        '/currency/create', '/currency/delete', '/currency/index', '/currency/synchronization', '/currency/update', '/currency/view',
        '/currency-history/create', '/currency-history/delete', '/currency-history/index', '/currency-history/update', '/currency-history/view',

//        '/product-type/create', '/product-type/delete', '/product-type/index', '/product-type/update', '/product-type/view',
        '/product/product-type-crud/create', '/product/product-type-crud/delete', '/product/product-type-crud/index', '/product/product-type-crud/update', '/product/product-type-crud/view',

        //'/product/create', '/product/create-ajax', '/product/delete', '/product/delete-ajax', '/product/index', '/product/update', '/product/view',
        '/product/product-crud/create', '/product/product-crud/index', '/product/product-crud/update', '/product/product-crud/view', '/product/product-crud/delete',
        '/product/product/create-ajax', '/product/product/delete-ajax',

//        '/product-option/create', '/product-option/delete', '/product-option/index', '/product-option/update', '/product-option/view',
        '/product/product-option-crud/create', '/product/product-option-crud/delete', '/product/product-option-crud/index', '/product/product-option-crud/update', '/product/product-option-crud/view',

//        '/product-quote/create', '/product-quote/delete', '/product-quote/delete-ajax', '/product-quote/index', '/product-quote/update', '/product-quote/view',
        '/product/product-quote-crud/create', '/product/product-quote-crud/delete', '/product/product-quote-crud/index', '/product/product-quote-crud/update', '/product/product-quote-crud/view',
        '/product/product-quote/delete-ajax',

        '/product/product-quote-status-log-crud/index', '/product/product-quote-status-log-crud/view', '/product/product-quote-status-log-crud/create', '/product/product-quote-status-log-crud/update', '/product/product-quote-status-log-crud/delete',

//        '/product-quote-option/create', '/product-quote-option/create-ajax', '/product-quote-option/delete', '/product-quote-option/delete-ajax', '/product-quote-option/index', '/product-quote-option/update', '/product-quote-option/update-ajax', '/product-quote-option/view',
        '/product/product-quote-option-crud/create', '/product/product-quote-option-crud/delete', '/product/product-quote-option-crud/index', '/product/product-quote-option-crud/update', '/product/product-quote-option-crud/view',
        '/product/product-quote-option/create-ajax', '/product/product-quote-option/delete-ajax', '/product/product-quote-option/update-ajax',

        '/order/create', '/order/create-ajax', '/order/delete', '/order/delete-ajax', '/order/index', '/order/list-menu-ajax', '/order/update', '/order/update-ajax', '/order/view',

//        '/offer/create', '/offer/create-ajax', '/offer/delete', '/offer/delete-ajax', '/offer/index', '/offer/list-menu-ajax', '/offer/update', '/offer/update-ajax', '/offer/view',
        '/offer/offer-crud/index', '/offer/offer-crud/create', '/offer/offer-crud/view', '/offer/offer-crud/delete', '/offer/offer-crud/update',
        '/offer/offer/create-ajax', '/offer/offer/delete-ajax', '/offer/offer/list-menu-ajax', '/offer/offer/update-ajax',


//        '/offer-product/create', '/offer-product/create-ajax', '/offer-product/delete', '/offer-product/delete-ajax', '/offer-product/index', '/offer-product/update', '/offer-product/view',
        '/offer/offer-product-crud/create', '/offer/offer-product-crud/delete', '/offer/offer-product-crud/index', '/offer/offer-product-crud/update', '/offer/offer-product-crud/view',
        '/offer/offer-product/create-ajax', '/offer/offer-product/delete-ajax',

        '/order-product/create', '/order-product/create-ajax', '/order-product/delete', '/order-product/delete-ajax', '/order-product/index', '/order-product/update', '/order-product/view',
        '/invoice/create', '/invoice/create-ajax', '/invoice/delete', '/invoice/delete-ajax', '/invoice/index', '/invoice/update', '/invoice/update-ajax', '/invoice/view',
        '/billing-info/create', '/billing-info/delete', '/billing-info/index', '/billing-info/update', '/billing-info/view',
        '/credit-card/create', '/credit-card/delete', '/credit-card/index', '/credit-card/update', '/credit-card/view',
        '/payment/create', '/payment/delete', '/payment/index', '/payment/update', '/payment/view',
        '/payment-method/create', '/payment-method/delete', '/payment-method/index', '/payment-method/update', '/payment-method/view',
    ];
}
