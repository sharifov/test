<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;
use yii\helpers\Inflector;

/**
 * Class m201207_080507_add_setting_metrics_enable
 */
class m201207_080507_add_setting_metrics_enable extends Migration
{
    private string $name = 'Metrics enabled';
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
                's_type' => Setting::TYPE_BOOL,
                's_value' => false,
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
