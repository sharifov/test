<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;
use yii\helpers\Inflector;

/**
 * Class m201214_122325_add_settings_for_cleaner
 */
class m201214_122325_add_settings_for_cleaner extends Migration
{
    private string $nameCleanCalls = 'Clean Call after days';
    private $keyCleanCalls;

    private string $nameCleanUserMonitor = 'Clean User Monitor after days';
    private $keyCleanUserMonitor;

    public function init()
    {
        $this->keyCleanCalls = Inflector::slug($this->nameCleanCalls, '_');
        $this->keyCleanUserMonitor = Inflector::slug($this->nameCleanUserMonitor, '_');
        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Cleaner');
        $this->insert(
            '{{%setting}}',
            [
                's_key' => $this->keyCleanCalls,
                's_name' => $this->nameCleanCalls,
                's_type' => Setting::TYPE_INT,
                's_value' => 10,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );
        $this->insert(
            '{{%setting}}',
            [
                's_key' => $this->keyCleanUserMonitor,
                's_name' => $this->nameCleanUserMonitor,
                's_type' => Setting::TYPE_INT,
                's_value' => 7,
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
            $this->keyCleanCalls, $this->keyCleanUserMonitor
        ]]);
    }
}
