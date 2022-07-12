<?php

use modules\objectSegment\src\contracts\ObjectSegmentListContract;
use yii\db\Migration;

/**
 * Class m220712_101037_add_new_site_setting_client_return_object_segment_list_keys
 */
class m220712_101037_add_new_site_setting_client_return_object_segment_list_keys extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'client_return_segment_list_keys',
                's_name' => 'Client Return Object Segment List Keys',
                's_type' => \common\models\Setting::TYPE_ARRAY,
                's_value' => json_encode([
                    ObjectSegmentListContract::OBJECT_SEGMENT_LIST_KEY_CLIENT_RETURN,
                    ObjectSegmentListContract::OBJECT_SEGMENT_LIST_KEY_CLIENT_RETURN_DIAMOND,
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
            'client_return_segment_list_keys',
        ]]);
        Yii::$app->db->getSchema()->refreshTableSchema('{{%setting}}');
    }
}
