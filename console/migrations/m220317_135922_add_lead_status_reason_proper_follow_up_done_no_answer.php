<?php

use yii\db\Migration;

/**
 * Class m220317_135922_add_lead_status_reason_proper_follow_up_done_no_answer
 */
class m220317_135922_add_lead_status_reason_proper_follow_up_done_no_answer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $this->delete('{{%lead_status_reason}}', [
                'IN',
                'lsr_key',
                [
                    'proper_follow_up_done_no_answer',
                ]
            ]);
            $this->insert('{{%lead_status_reason}}', [
                'lsr_key'              => 'proper_follow_up_done_no_answer',
                'lsr_name'             => 'Proper Follow Up Done (No Answer)',
                'lsr_description'      => 'Only applies to Leads In Extra Queue when expiration time (10 days by default) ends',
                'lsr_enabled'          => 1,
                'lsr_comment_required' => 0,
                'lsr_params'           => json_encode([
                    'count_to_conversion'         => true,
                    'double_resolution_principle' => true,
                    'qa_test'                     => false
                ]),
                'lsr_created_user_id'  => null,
                'lsr_updated_user_id'  => null,
                'lsr_created_dt'       => date('Y-m-d H:i:s'),
                'lsr_updated_dt'       => date('Y-m-d H:i:s')
            ]);
        } catch (Throwable $throwable) {
            Yii::error(
                $throwable,
                'm220317_135922_add_lead_status_reason_proper_follow_up_done_no_answer:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            $this->delete('{{%lead_status_reason}}', [
                'IN',
                'lsr_key',
                [
                    'proper_follow_up_done_no_answer',
                ]
            ]);
        } catch (Throwable $throwable) {
            Yii::error(
                $throwable,
                'm220317_135922_add_lead_status_reason_proper_follow_up_done_no_answer:safeDown:Throwable'
            );
        }
    }
}
