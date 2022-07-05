<?php

use yii\db\Migration;

/**
 * Class m220630_113900_add_site_setting_email_otp_subject
 */
class m220630_113900_add_site_setting_email_otp_subject extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $setting = (new \yii\db\Query())->select(['s_value'])->from('{{%setting}}')->where(['s_key' => 'otp_email_settings'])->one();
        if ($setting) {
            $key = \yii\helpers\Json::decode($setting['s_value']);
            $key['template_subject'] = 'Your Two-Factor verification code!';
            $this->update('{{%setting}}', ['s_value' => json_encode($key)], ['s_key' => 'otp_email_settings']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
