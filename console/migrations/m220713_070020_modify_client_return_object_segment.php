<?php

use modules\objectSegment\src\contracts\ObjectSegmentListContract;
use modules\objectSegment\src\entities\ObjectSegmentList;
use modules\objectSegment\src\entities\ObjectSegmentRule;
use yii\db\Migration;

/**
 * Class m220713_070020_modify_client_return_object_segment
 */
class m220713_070020_modify_client_return_object_segment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $objectSegmentListRecord = ObjectSegmentList
            ::find()
            ->where(['osl_key' => ObjectSegmentListContract::OBJECT_SEGMENT_LIST_KEY_CLIENT_RETURN])
            ->limit(1)
            ->one();
        if (empty($objectSegmentListRecord)) {
            return;
        }

        ObjectSegmentRule::deleteAll(['osr_osl_id' => $objectSegmentListRecord->osl_id]);

        $this->insert('{{%object_segment_rules}}', [
            'osr_osl_id' => $objectSegmentListRecord->osl_id,
            'osr_title' => 'Client Return',
            'osr_rule_condition' => '(r.sub.count_sold_leads >= 2)',
            'osr_rule_condition_json' => '{"condition":"AND","rules":[{"id":"clientcount_sold_leads","field":"count_sold_leads","type":"integer","input":"number","operator":"greater_or_equal","value":1}],"valid":true}',
            'osr_created_dt' => date('Y-m-d H:i:s'),
            'osr_enabled' => 1
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}
