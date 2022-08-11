<?php

use modules\objectSegment\src\contracts\ObjectSegmentListContract;
use yii\db\Migration;

/**
 * Class m220712_065723_add_new_object_segment_rules
 */
class m220712_065723_add_new_object_segment_rules extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $objectSegmentList = (new \yii\db\Query())->from('{{%object_segment_list}}')->select(['osl_id'])->where(['osl_key' => ObjectSegmentListContract::OBJECT_SEGMENT_LIST_KEY_CLIENT_RETURN])->limit(1)->one();
        if ($objectSegmentList) {
            $this->insert('{{%object_segment_rules}}', [
                'osr_osl_id' => $objectSegmentList['osl_id'],
                'osr_title' => 'Client Return',
                'osr_rule_condition' => '(r.sub.count_sold_leads >= 1)',
                'osr_rule_condition_json' => '{"condition":"AND","rules":[{"id":"clientcount_sold_leads","field":"count_sold_leads","type":"integer","input":"number","operator":"greater_or_equal","value":1}],"valid":true}',
                'osr_created_dt' => date('Y-m-d H:i:s'),
                'osr_enabled' => 1
            ]);
        }
        $objectSegmentList = (new \yii\db\Query())->from('{{%object_segment_list}}')->select(['osl_id'])->where(['osl_key' => ObjectSegmentListContract::OBJECT_SEGMENT_LIST_KEY_CLIENT_RETURN_DIAMOND])->limit(1)->one();
        $this->insert('{{%object_segment_rules}}', [
            'osr_osl_id' => $objectSegmentList['osl_id'],
            'osr_title' => 'Client Return Diamond',
            'osr_rule_condition' => '(r.sub.count_sold_leads >= 10)',
            'osr_rule_condition_json' => '{"condition":"AND","rules":[{"id":"clientcount_sold_leads","field":"count_sold_leads","type":"integer","input":"number","operator":"greater_or_equal","value":10}],"valid":true}',
            'osr_created_dt' => date('Y-m-d H:i:s'),
            'osr_enabled' => 1
        ]);
        Yii::$app->objectSegment->invalidatePolicyCache();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $objectSegmentList = (new \yii\db\Query())->from('{{%object_segment_list}}')->select(['osl_id'])->where(['osl_key' => ObjectSegmentListContract::OBJECT_SEGMENT_LIST_KEY_CLIENT_RETURN])->limit(1)->one();
        if ($objectSegmentList) {
            $this->delete('{{%object_segment_rules}}', ['osr_osl_id' => $objectSegmentList['osl_id']]);
            Yii::$app->objectSegment->invalidatePolicyCache();
        }
        $objectSegmentList = (new \yii\db\Query())->from('{{%object_segment_list}}')->select(['osl_id'])->where(['osl_key' => ObjectSegmentListContract::OBJECT_SEGMENT_LIST_KEY_CLIENT_RETURN_DIAMOND])->limit(1)->one();
        if ($objectSegmentList) {
            $this->delete('{{%object_segment_rules}}', ['osr_osl_id' => $objectSegmentList['osl_id']]);
            Yii::$app->objectSegment->invalidatePolicyCache();
        }
    }
}
