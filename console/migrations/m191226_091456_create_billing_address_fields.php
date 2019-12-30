<?php

use yii\db\Migration;

/**
 * Class m191226_091456_create_billing_address_fields
 */
class m191226_091456_create_billing_address_fields extends Migration
{

    public $routes = [
        '/credit-card/*',
        '/billing-info/*',
        '/payment-method/*',
        '/payment/*',
    ];

    public $roles = [
        \common\models\Employee::ROLE_ADMIN,
        \common\models\Employee::ROLE_SUPER_ADMIN,
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }


//        $this->dropTable('{{%transaction}}');
//        $this->dropTable('{{%payment}}');
//        $this->dropTable('{{%payment_method}}');
//        $this->dropTable('{{%billing_info}}');
//        $this->dropTable('{{%credit_card}}');

        //$this->dropForeignKey('FK-billing_info-bi_cc_id', '{{%credit_card}}');
        //$this->dropForeignKey('FK-billing_info-bi_cc_id', '{{%billing_info}}');
        //$this->dropTable('{{%credit_card}}');
        //$this->dropTable('{{%billing_info}}');

        $this->createTable('{{%credit_card}}',	[
            'cc_id'				        => $this->primaryKey(),
            'cc_number'     	        => $this->string(32)->notNull(),
            'cc_display_number'	        => $this->string(18),
            'cc_holder_name'	        => $this->string(50),
            'cc_expiration_month'       => $this->tinyInteger(2)->notNull(),
            'cc_expiration_year'        => $this->smallInteger()->notNull(),
            'cc_cvv'                    => $this->string(16),
            'cc_type_id'                => $this->tinyInteger(1),
            'cc_status_id'              => $this->tinyInteger(1)->defaultValue(0),
            'cc_is_expired'             => $this->boolean()->defaultValue(false),
            'cc_created_user_id'        => $this->integer(),
            'cc_updated_user_id'        => $this->integer(),
            'cc_created_dt'             => $this->dateTime(),
            'cc_updated_dt'             => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey(
            'FK-credit_card-cc_created_user_id',
            '{{%credit_card}}',
            'cc_created_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-credit_card-cc_updated_user_id',
            '{{%credit_card}}',
            'cc_updated_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );



        $this->createTable('{{%billing_info}}',	[
            'bi_id'				        => $this->primaryKey(),
            'bi_first_name'   	        => $this->string(30)->notNull(),
            'bi_last_name'	            => $this->string(30)->notNull(),
            'bi_middle_name'            => $this->string(30),
            'bi_company_name'           => $this->string(40),
            'bi_address_line1'          => $this->string(50)->notNull(),
            'bi_address_line2'          => $this->string(50),

            'bi_city'                   => $this->string(30)->notNull(),
            'bi_state'                  => $this->string(40),
            'bi_country'                => $this->string(2)->notNull(),
            'bi_zip'                    => $this->string(10),

            'bi_contact_phone'          => $this->string(20),
            'bi_contact_email'          => $this->string(160),
            'bi_contact_name'           => $this->string(60),

            'bi_payment_method_id'      => $this->tinyInteger(),
            'bi_cc_id'                  => $this->integer(),

            'bi_order_id'               => $this->integer(),
            'bi_status_id'              => $this->tinyInteger(1),

            'bi_created_user_id'        => $this->integer(),
            'bi_updated_user_id'        => $this->integer(),
            'bi_created_dt'             => $this->dateTime(),
            'bi_updated_dt'             => $this->dateTime(),
        ], $tableOptions);


        $this->addForeignKey(
            'FK-billing_info-bi_cc_id',
            '{{%billing_info}}',
            'bi_cc_id',
            '{{%credit_card}}',
            'cc_id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-billing_info-bi_order_id',
            '{{%billing_info}}',
            'bi_order_id',
            '{{%order}}',
            'or_id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-billing_info-bi_created_user_id',
            '{{%billing_info}}',
            'bi_created_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-billing_info-bi_updated_user_id',
            '{{%billing_info}}',
            'bi_updated_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );


        $this->createTable('{{%payment_method}}',	[
            'pm_id'		                => $this->primaryKey(),
            'pm_name'                   => $this->string(50)->unique()->notNull(),
            'pm_short_name'             => $this->string(20),
            'pm_enabled'   	            => $this->boolean()->defaultValue(true),
            'pm_category_id'            => $this->tinyInteger(1),
            'pm_updated_user_id'        => $this->integer(),
            'pm_updated_dt'             => $this->dateTime(),
        ], $tableOptions);


        $this->addForeignKey(
            'FK-payment_method-pm_updated_user_id',
            '{{%payment_method}}',
            'pm_updated_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $payMethodList = [];
        $payMethodList[] = ['pm_id' => 1, 'pm_name' => 'Credit / Debit Card', 'pm_short_name' => 'CC', 'pm_enabled' => true];
        $payMethodList[] = ['pm_id' => 2, 'pm_name' => 'Direct Debit', 'pm_short_name' => 'DD', 'pm_enabled' => false];
        $payMethodList[] = ['pm_id' => 3, 'pm_name' => 'eWallets', 'pm_short_name' => 'EW', 'pm_enabled' => true];
        $payMethodList[] = ['pm_id' => 4, 'pm_name' => 'Bank Transfers', 'pm_short_name' => 'BT', 'pm_enabled' => false];
        $payMethodList[] = ['pm_id' => 5, 'pm_name' => 'Real-time Banking', 'pm_short_name' => 'RB', 'pm_enabled' => false];
        $payMethodList[] = ['pm_id' => 6, 'pm_name' => 'Cash & PrePaid Vouchers', 'pm_short_name' => 'CH', 'pm_enabled' => false];
        $payMethodList[] = ['pm_id' => 7, 'pm_name' => 'Mobile Payments', 'pm_short_name' => 'MP', 'pm_enabled' => false];

        foreach($payMethodList as $payMethod) {
            $payMethod['pm_updated_dt'] = date('Y-m-d H:i:s');
            $this->insert('{{%payment_method}}', $payMethod);
        }


        $this->createTable('{{%payment}}',	[
            'pay_id'		            => $this->primaryKey(),
            'pay_type_id'               => $this->tinyInteger(1),
            'pay_method_id'             => $this->integer(),
            'pay_status_id'             => $this->tinyInteger(1)->defaultValue(0),         // Canceled, Completed, Disputed, Challenged

            'pay_date'     	            => $this->date()->notNull(),
            'pay_amount'   	            => $this->decimal(8, 2)->notNull(),
            'pay_currency'   	        => $this->string(3),

            'pay_invoice_id'            => $this->integer(),
            'pay_order_id'              => $this->integer(),

            'pay_created_user_id'       => $this->integer(),
            'pay_updated_user_id'       => $this->integer(),
            'pay_created_dt'            => $this->dateTime(),
            'pay_updated_dt'            => $this->dateTime(),
        ], $tableOptions);


        $this->addForeignKey(
            'FK-payment-pay_method_id',
            '{{%payment}}',
            'pay_method_id',
            '{{%payment_method}}',
            'pm_id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey('FK-payment-pay_currency', '{{%payment}}', ['pay_currency'], '{{%currency}}', ['cur_code'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-payment-pay_invoice_id', '{{%payment}}', ['pay_invoice_id'], '{{%invoice}}', ['inv_id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-payment-pay_order_id', '{{%payment}}', ['pay_order_id'], '{{%order}}', ['or_id'], 'SET NULL', 'CASCADE');

        $this->addForeignKey(
            'FK-payment-pay_created_user_id',
            '{{%payment}}',
            'pay_created_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'FK-payment-pay_updated_user_id',
            '{{%payment}}',
            'pay_updated_user_id',
            '{{%employees}}',
            'id',
            'SET NULL',
            'CASCADE'
        );


        $this->createTable('{{%transaction}}',	[
            'tr_id'		                => $this->primaryKey(),
            'tr_code'   	            => $this->string(30),
            'tr_invoice_id'             => $this->integer(),
            'tr_payment_id'             => $this->integer(),
            'tr_type_id'                => $this->tinyInteger(1), // Payment, Refund, Adjustment
            'tr_date'     	            => $this->date()->notNull(),
            'tr_amount'   	            => $this->decimal(8, 2)->notNull(),
            'tr_currency'   	        => $this->string(3),

            'tr_created_dt'             => $this->dateTime(),
        ], $tableOptions);

        $this->addForeignKey('FK-transaction-tr_currency', '{{%transaction}}', ['tr_currency'], '{{%currency}}', ['cur_code'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-transaction-tr_invoice_id', '{{%transaction}}', ['tr_invoice_id'], '{{%invoice}}', ['inv_id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-transaction-tr_payment_id', '{{%transaction}}', ['tr_payment_id'], '{{%payment}}', ['pay_id'], 'SET NULL', 'CASCADE');



/*
 *
Credit Card Type	Credit Card Number
American Express	371449635398431
Diners Club	30569309025904
Discover	6011111111111117
JCB	3530111333300000
MasterCard	5555555555554444
Visa	4111111111111111
 */



//        readonly attribute DOMString city;
//  readonly attribute DOMString country;
//  readonly attribute DOMString dependentLocality;
//  readonly attribute DOMString organization;
//  readonly attribute DOMString phone;
//  readonly attribute DOMString postalCode;
//  readonly attribute DOMString recipient;
//  readonly attribute DOMString region;
//  readonly attribute DOMString sortingCode;
//  readonly attribute FrozenArray<DOMString> addressLine;



//        gr:PaymentMethodCreditCard
//Predefined Individuals
//
//gr:AmericanExpress
//gr:DinersClub
//gr:Discover
//gr:JCB
//gr:MasterCard
//gr:VISA


//        gr:ByBankTransferInAdvance
//gr:ByInvoice
//gr:Cash
//gr:CheckInAdvance
//gr:COD
//gr:DirectDebit
//gr:GoogleCheckout
//gr:PayPal
//gr:PaySwarm
//
//
//        PaymentMethod

        //$this->addPrimaryKey('PK-currency-cur_code', '{{%currency_history}}', ['cur_his_code', 'cur_his_created']);

        (new \console\migrations\RbacMigrationService())->up($this->routes, $this->roles);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

//        $this->dropForeignKey('FK-billing_info-bi_cc_id', '{{%billing_info}}');
//        $this->dropForeignKey('FK-billing_info-bi_order_id', '{{%billing_info}}');
//        $this->dropForeignKey('FK-transaction-tr_payment_id', '{{%transaction}}');

        $this->dropTable('{{%transaction}}');
        $this->dropTable('{{%payment}}');
        $this->dropTable('{{%payment_method}}');
        $this->dropTable('{{%billing_info}}');
        $this->dropTable('{{%credit_card}}');

        (new \console\migrations\RbacMigrationService())->down($this->routes, $this->roles);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

}
