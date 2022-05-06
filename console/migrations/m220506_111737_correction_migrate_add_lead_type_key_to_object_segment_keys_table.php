<?php

use yii\db\Migration;

/**
 * Class m220506_111737_correction_migrate_add_lead_type_key_to_object_segment_keys_table
 */
class m220506_111737_correction_migrate_add_lead_type_key_to_object_segment_keys_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $objectSegmentType = \modules\objectSegment\src\entities\ObjectSegmentType
                ::find()
                ->where(['ost_key' => \modules\objectSegment\src\contracts\ObjectSegmentKeyContract::TYPE_KEY_LEAD])
                ->one();
            if (isset($objectSegmentType)) {
                return;
            }
            $this->insert(
                '{{%object_segment_types}}',
                [
                    'ost_key'         =>    \modules\objectSegment\src\contracts\ObjectSegmentKeyContract::TYPE_KEY_LEAD,
                ]
            );
            \Yii::$app->db->getSchema()->refreshTableSchema('{{%object_segment_types}}');
        } catch (\Throwable $throwable) {
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($throwable),
                'm220506_111737_correction_migrate_add_lead_type_key_to_object_segment_keys_table:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return;
    }
}
