<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m220419_114345_add_settings_accepted_quote_refund_statuses
 */
class m220419_114345_add_settings_accepted_quote_refund_statuses extends Migration
{
    public const TABLE_SETTING_CATEGORY = '{{%setting_category}}';
    public const TABLE_SETTING = '{{%setting}}';

    public const CATEGORY_NAME = 'Product Quote Change';

    /**
     * @return void
     */
    public function safeUp()
    {
        $settingCategory = $this->getSettingCategory();

        if (!$settingCategory) {
            $this->insert(self::TABLE_SETTING_CATEGORY, [
                'sc_name' => self::CATEGORY_NAME
            ]);
            $settingCategory = $this->getSettingCategory();
        }

        if ($settingCategory) {
            $this->insert(self::TABLE_SETTING, [
                's_key' => 'accepted_quote_refund_statuses',
                's_name' => 'Refund statuses of the accepted product quote ',
                's_type' => 'array',
                's_value' => json_encode([
                    5 => 'COMPLETED',
                    7 => 'PROCESSING',
                ]),
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory['sc_id'],
                's_description' => 'Refund statuses of the accepted product quote for Voluntary Exchange',
            ]);

            if (Yii::$app->cache) {
                Yii::$app->cache->flush();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete(self::TABLE_SETTING, ['s_key' => 'accepted_quote_refund_statuses']);
    }

    /**
     * @return array|bool
     */
    private function getSettingCategory()
    {
        return (new Query())->select('sc_id')
            ->from(self::TABLE_SETTING_CATEGORY)
            ->where(['sc_name' => self::CATEGORY_NAME])
            ->one();
    }
}
