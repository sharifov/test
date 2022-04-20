<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m211202_082314_add_settings_db_view_crypt
 */
class m211202_082314_add_settings_db_view_crypt extends Migration
{
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('System');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'db_crypt_block_encryption_mode',
                's_name' => 'Block encryption mode',
                's_type' => Setting::TYPE_STRING,
                's_value' => 'aes-128-ecb',
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => null,
            ]
        );
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'db_crypt_key_str',
                's_name' => 'Key str',
                's_type' => Setting::TYPE_STRING,
                's_value' => 'zjX2m2Xd2tQjGtydxkFVWXVmBsMg7LnG',
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => null,
            ]
        );
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'db_crypt_init_vector',
                's_name' => 'Init vector',
                's_type' => Setting::TYPE_STRING,
                's_value' => 'DeBijpCtvFCO0bHU',
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => null,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'db_crypt_block_encryption_mode',
            'db_crypt_key_str',
            'db_crypt_init_vector',
        ]]);
    }
}
