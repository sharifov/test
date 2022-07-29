<?php

use yii\db\Migration;

/**
 * Class m220727_063730_add_closing_reason
 */
class m220727_063730_add_closing_reason extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%lead_status_reason}}', [
            'lsr_key' => 'proper_follow_up_done_never_answered',
            'lsr_name' => 'Proper Follow Up Done (Client never answered)',
            'lsr_description' => 'Only applies to the following Leads:

1) The client has never responded to any contact attempts from the agent.

2) Agent has to complete the minimum required follow up of 3 days. 
1st day (2 calls + 3 PQs (emails/SMS) or 1 PQ (if it\'s rush or specific). 
2nd day  (2 calls + 1 PQ (emails/SMS) + FU SMS/email (optional)). 
3rd day  (2 calls + 1 PQ (emails/SMS) + FU SMS/email (optional)).
',
            'lsr_enabled' => 1,
            'lsr_comment_required' => 0,
            'lsr_params' => json_encode([
                'count_to_conversion' => false,
                'double_resolution_principle' => true,
                'qa_test' => true
            ]),
            'lsr_created_user_id' => null,
            'lsr_updated_user_id' => null,
            'lsr_created_dt' => date('Y-m-d H:i:s'),
            'lsr_updated_dt' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%lead_status_reason}}', [
            'lsr_key' => [
                'proper_follow_up_done_never_answered'
            ]
        ]);
    }
}
