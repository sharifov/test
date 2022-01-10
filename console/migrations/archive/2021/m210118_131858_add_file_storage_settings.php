<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m210118_131858_add_file_storage_settings
 */
class m210118_131858_add_file_storage_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('File storage');
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'file_storage_enabled',
                's_name' => 'File storage enabled',
                's_type' => Setting::TYPE_BOOL,
                's_value' => false,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'file_upload_enabled',
                's_name' => 'File storage upload enabled',
                's_type' => Setting::TYPE_BOOL,
                's_value' => true,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'file_download_enabled',
                's_name' => 'File storage download enabled',
                's_type' => Setting::TYPE_BOOL,
                's_value' => true,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'file_upload_max_size',
                's_name' => 'File storage upload max size (Mb)',
                's_type' => Setting::TYPE_INT,
                's_value' => 2,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'file_upload_allowed_mime_types',
                's_name' => 'File storage upload allowed mime types',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    'image/jpeg',
                    'image/png',
                    'text/plain',
                    'application/pdf',
                    'application/msword',
                ]),
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'file_upload_user_period_hours',
                's_name' => 'File storage upload user period hours',
                's_type' => Setting::TYPE_INT,
                's_value' => 24,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'file_upload_user_period_limit',
                's_name' => 'File storage upload user period limit',
                's_type' => Setting::TYPE_INT,
                's_value' => 100,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'file_email_attach_enabled',
                's_name' => 'File storage email attach enabled',
                's_type' => Setting::TYPE_BOOL,
                's_value' => true,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'file_storage_enabled',
            'file_upload_enabled',
            'file_download_enabled',
            'file_upload_max_size',
            'file_upload_allowed_mime_types',
            'file_upload_user_period_hours',
            'file_upload_user_period_limit',
            'file_email_attach_enabled',
        ]]);
    }
}
