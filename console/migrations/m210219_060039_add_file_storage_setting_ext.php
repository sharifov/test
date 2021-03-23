<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m210219_060039_add_file_storage_setting_ext
 */
class m210219_060039_add_file_storage_setting_ext extends Migration
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
                's_key' => 'file_upload_allow_ext',
                's_name' => 'File storage upload allowed extensions',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    'doc', 'docx', 'odt', 'rtf',
                    'xls', 'xlsx', 'ods',
                    'pdf', 'txt',
                    'jpg', 'jpeg', 'gif', 'png'
                ]),
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
            'file_upload_allow_ext',
        ]]);
    }
}
