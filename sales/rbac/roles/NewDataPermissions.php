<?php

namespace sales\rbac\roles;

class NewDataPermissions
{
    public const PERMISSIONS = [
        '/currency/create', '/currency/delete', '/currency/index', '/currency/synchronization', '/currency/update', '/currency/view',
        '/currency-history/create', '/currency-history/delete', '/currency-history/index', '/currency-history/update', '/currency-history/view',
        '/product-type/create', '/product-type/delete', '/product-type/index', '/product-type/update', '/product-type/view',
        '/product/create', '/product/create-ajax', '/product/delete', '/product/delete-ajax', '/product/index', '/product/update', '/product/view',
        '/product-option/create', '/product-option/delete', '/product-option/index', '/product-option/update', '/product-option/view',
        '/product-quote/create', '/product-quote/delete', '/product-quote/delete-ajax', '/product-quote/index', '/product-quote/update', '/product-quote/view',
        '/product-quote-option/create', '/product-quote-option/create-ajax', '/product-quote-option/delete', '/product-quote-option/delete-ajax', '/product-quote-option/index', '/product-quote-option/update', '/product-quote-option/update-ajax', '/product-quote-option/view',
        '/order/create', '/order/create-ajax', '/order/delete', '/order/delete-ajax', '/order/index', '/order/list-menu-ajax', '/order/update', '/order/update-ajax', '/order/view',
        '/offer/create', '/offer/create-ajax', '/offer/delete', '/offer/delete-ajax', '/offer/index', '/offer/list-menu-ajax', '/offer/update', '/offer/update-ajax', '/offer/view',
        '/offer-product/create', '/offer-product/create-ajax', '/offer-product/delete', '/offer-product/delete-ajax', '/offer-product/index', '/offer-product/update', '/offer-product/view',
        '/order-product/create', '/order-product/create-ajax', '/order-product/delete', '/order-product/delete-ajax', '/order-product/index', '/order-product/update', '/order-product/view',
        '/invoice/create', '/invoice/create-ajax', '/invoice/delete', '/invoice/delete-ajax', '/invoice/index', '/invoice/update', '/invoice/update-ajax', '/invoice/view',
        '/billing-info/create', '/billing-info/delete', '/billing-info/index', '/billing-info/update', '/billing-info/view',
        '/credit-card/create', '/credit-card/delete', '/credit-card/index', '/credit-card/update', '/credit-card/view',
        '/payment/create', '/payment/delete', '/payment/index', '/payment/update', '/payment/view',
        '/payment-method/create', '/payment-method/delete', '/payment-method/index', '/payment-method/update', '/payment-method/view',
    ];
}
