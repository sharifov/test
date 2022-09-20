<?php

use common\models\Lead;
use common\models\Setting;
use yii\db\Migration;

/**
 * Class m220915_093806_add_new_setting_case_cross_sale_queue
 */
class m220915_093806_add_new_setting_case_cross_sale_queue extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'case_cross_sale_queue',
                's_name' => 'Case Cross Sale queue',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    'excludeProjects' => [
                        'priceline',
                        'kayak',
                    ],
                    'excludeCabin' => [
                        Lead::getCabin(Lead::CABIN_BUSINESS),
                    ],
                ]),
                's_updated_dt' => date('Y-m-d H:i:s'),
            ]
        );
        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'case_cross_sale_queue',
        ]]);
        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
