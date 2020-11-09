<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;
use yii\helpers\Inflector;

/**
 * Class m201109_104133_add_setting_processing_fee
 */
class m201109_104133_add_setting_processing_fee extends Migration
{
    private string $name = 'Processing Fee';
    private $key;

    public function init()
    {
        $this->key = Inflector::slug($this->name, '_');
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('General');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => $this->key,
                's_name' => $this->name,
                's_type' => Setting::TYPE_DOUBLE,
                's_value' => 25.00,
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
            $this->key,
        ]]);
    }
}
