<?php

use yii\db\Migration;

/**
 * Class m200603_200350_add_in_site_settings_new_params
 */
class m200603_200350_add_in_site_settings_new_params extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $caseSaleTicketEmailData = Yii::$app->params['settings']['case_sale_ticket_email_data'];

        $caseSaleTicketEmailData['emailOnRecallCommChanged'] = [];

        $this->update('{{%setting}}', [
            's_value' => json_encode($caseSaleTicketEmailData)
        ], ['s_key' => 'case_sale_ticket_email_data']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
