<?php

use modules\objectSegment\src\contracts\ObjectSegmentKeyContract;
use modules\objectSegment\src\contracts\ObjectSegmentListContract;
use modules\objectSegment\src\entities\ObjectSegmentList;
use modules\objectSegment\src\entities\ObjectSegmentType;
use yii\db\Migration;

/**
 * Class m220713_071241_add_new_client_return_object_segment
 */
class m220713_071241_add_new_client_return_object_segment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $objectSegmentType = ObjectSegmentType
            ::find()
            ->where(['ost_key' => ObjectSegmentKeyContract::TYPE_KEY_CLIENT])
            ->limit(1)
            ->one();

        $this->insert(
            '{{%object_segment_list}}',
            [
                'osl_ost_id' => $objectSegmentType->ost_id,
                'osl_title' => 'New',
                'osl_key'         =>    ObjectSegmentListContract::OBJECT_SEGMENT_LIST_KEY_CLIENT_NEW,
                'osl_enabled' => true,
                'osl_description' => 'New Client',
                'osl_is_system' => true,
            ]
        );

        $objectSegmentListRecord = ObjectSegmentList
            ::find()
            ->where(['osl_key' => ObjectSegmentListContract::OBJECT_SEGMENT_LIST_KEY_CLIENT_NEW])
            ->limit(1)
            ->one();

        $this->insert('{{%object_segment_rules}}', [
            'osr_osl_id' => $objectSegmentListRecord->osl_id,
            'osr_title' => 'New Client',
            'osr_rule_condition' => '(r.sub.count_sold_leads < 2)',
            'osr_rule_condition_json' => '{"condition":"AND","rules":[{"id":"clientcount_sold_leads","field":"count_sold_leads","type":"integer","input":"number","operator":"less","value":2}],"valid":true}',
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
