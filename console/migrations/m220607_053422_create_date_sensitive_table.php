<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%date_sensitive}}`.
 */
class m220607_053422_create_date_sensitive_table extends Migration
{
    private const DEFAULT_KEY = 'view';
    private const SOURCE = [
        'contact_phone_list' => ['cpl_phone_number'],
        'phone_blacklist' => ['pbl_phone'],
        'email_list' => ['el_email'],
        'phone_list' => ['pl_phone_number'],
        'email' => [
            'e_email_from',
            'e_email_to',
            'e_email_cc',
            'e_email_bc',
            'e_email_body_text',
            'e_attach',
            'e_email_from_name',
            'e_email_to_name',
            'e_message_id',
            'e_ref_message_id',
        ],
        'leads' => [
            'l_client_first_name',
            'l_client_last_name',
            'l_client_phone',
            'l_client_email',
            'additional_information',
        ],
        'sms' => [
            's_phone_from',
            's_phone_to',
            's_sms_text',
            's_sms_data',
        ],
        'sms_distribution_list' => [
            'sdl_phone_from',
            'sdl_phone_to',
        ],
        'invoice' => [
            'inv_description',
        ],
        'flight_quote' => [
            'fq_json_booking',
            'fq_ticket_json',
        ],
        'api_user' => [
            'au_api_username',
            'au_api_password',
            'au_email',
        ],
        'attraction_pax' => [
            'atnp_first_name',
            'atnp_last_name',
        ],
        'hotel_room_pax' => [
            'hrp_first_name',
            'hrp_last_name',
        ],
        'hotel_quote' => [
            'hq_json_booking',
        ],
        'cruise_quote' => ['crq_data_json'],
        'credit_card' => [
            'cc_number',
            'cc_display_number',
            'cc_holder_name',
            'cc_expiration_month',
            'cc_expiration_year',
            'cc_cvv',
            'cc_security_hash',
        ],
        'clients' => [
            'first_name',
            'middle_name',
            'last_name',
        ],
        'client_phone' => ['phone'],
        'client_email' => ['email'],
        'client_account' => [
            'ca_username',
            'ca_first_name',
            'ca_middle_name',
            'ca_last_name',
            'ca_phone',
            'ca_email',
        ],
        //'client_account_social' => ['cas_identity'],
        'billing_info' => [
            'bi_first_name',
            'bi_last_name',
            'bi_middle_name',
            'bi_address_line1',
            'bi_address_line2',
            'bi_contact_phone',
            'bi_contact_email',
            'bi_contact_name',
        ],
        'call_log_record' => ['clr_record_sid'],
        'case_sale' => [
            'css_sale_data',
            'css_sale_data_updated',
        ],
        'conference' => [
            'cf_recording_url',
            'cf_recording_sid',
        ],
        'email_unsubscribe' => ['eu_email'],
        'employee_contact_info' => ['email_pass'],
        'lead_qcall' => ['lqc_call_from'],
        'projects' => ['api_key'],
        'call_log' => [
            'cl_phone_from',
            'cl_phone_to',
        ],
        'sale_ticket' => [
            'st_client_name',
        ],
        'call' => [
            'c_from',
            'c_to',
            'c_forwarded_from',
            'c_caller_name',
            'c_recording_url',
        ],
        'product_holder' => [
            'ph_first_name',
            'ph_last_name',
            'ph_middle_name',
            'ph_email',
            'ph_phone_number',
        ],
        'coupon' => [
            'c_code',
        ],
        'phone_blacklist_log' => ['pbll_phone'],
        'hotel_quote_room_pax' => ['hqrp_first_name', 'hqrp_last_name'],
        'order_contact' => [
            'oc_first_name',
            'oc_last_name',
            'oc_middle_name',
            'oc_email',
            'oc_phone_number',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%date_sensitive}}', [
            'da_id' => $this->primaryKey(),
            'da_key' => $this->string(50)->notNull()->unique(),
            'da_name' => $this->string(50)->notNull(),
            'da_source' => $this->json(),
            'da_created_dt' => $this->dateTime(),
            'da_updated_dt' => $this->dateTime(),
            'da_created_user_id' => $this->integer(),
            'da_updated_user_id' => $this->integer(),
        ]);
        $this->addForeignKey('FK-date_sensitive-da_created_user_id', '{{%date_sensitive}}', 'da_created_user_id', '{{%employees}}', 'id', 'SET NULL');
        $this->addForeignKey('FK-date_sensitive-da_updated_user_id', '{{%date_sensitive}}', 'da_updated_user_id', '{{%employees}}', 'id', 'SET NULL');

        $this->insert(
            '{{%date_sensitive}}',
            [
                'da_key' => self::DEFAULT_KEY,
                'da_name' => 'Default',
                'da_source' => json_encode(self::SOURCE),
                'da_created_dt' => date('Y-m-d H:i:s')
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%date_sensitive}}');
    }
}
