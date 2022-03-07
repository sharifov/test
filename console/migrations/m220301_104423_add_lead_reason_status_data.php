<?php

use yii\db\Migration;

/**
 * Class m220301_104423_add_lead_reason_status_data
 */
class m220301_104423_add_lead_reason_status_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%lead_status_reason}}', 'lsr_key', $this->string(50));
        $this->alterColumn('{{%lead_status_reason}}', 'lsr_name', $this->string(100));
        $this->insert('{{%lead_status_reason}}', [
            'lsr_key' => 'alternative',
            'lsr_name' => 'Alternative',
            'lsr_description' => '',
            'lsr_enabled' => 1,
            'lsr_comment_required' => 0,
            'lsr_params' => json_encode([
                'count_to_conversion' => false,
                'double_resolution_principle' => false,
                'qa_test' => true
            ]),
            'lsr_created_user_id' => null,
            'lsr_updated_user_id' => null,
            'lsr_created_dt' => date('Y-m-d H:i:s'),
            'lsr_updated_dt' => date('Y-m-d H:i:s')
        ]);

        $this->insert('{{%lead_status_reason}}', [
            'lsr_key' => 'booked_with_another_agent',
            'lsr_name' => 'Booked with another agent from our company',
            'lsr_description' => '',
            'lsr_enabled' => 1,
            'lsr_comment_required' => 0,
            'lsr_params' => json_encode([
                'count_to_conversion' => true,
                'double_resolution_principle' => true,
                'qa_test' => false
            ]),
            'lsr_created_user_id' => null,
            'lsr_updated_user_id' => null,
            'lsr_created_dt' => date('Y-m-d H:i:s'),
            'lsr_updated_dt' => date('Y-m-d H:i:s')
        ]);

        $this->insert('{{%lead_status_reason}}', [
            'lsr_key' => 'canceled_trip',
            'lsr_name' => 'Canceled trip',
            'lsr_description' => '',
            'lsr_enabled' => 1,
            'lsr_comment_required' => 0,
            'lsr_params' => json_encode([
                'count_to_conversion' => true,
                'double_resolution_principle' => true,
                'qa_test' => false
            ]),
            'lsr_created_user_id' => null,
            'lsr_updated_user_id' => null,
            'lsr_created_dt' => date('Y-m-d H:i:s'),
            'lsr_updated_dt' => date('Y-m-d H:i:s')
        ]);

        $this->insert('{{%lead_status_reason}}', [
            'lsr_key' => 'client_asked_not_to_be_contacted_again',
            'lsr_name' => 'Client asked not to be contacted again',
            'lsr_description' => '',
            'lsr_enabled' => 1,
            'lsr_comment_required' => 0,
            'lsr_params' => json_encode([
                'count_to_conversion' => true,
                'double_resolution_principle' => false,
                'qa_test' => false
            ]),
            'lsr_created_user_id' => null,
            'lsr_updated_user_id' => null,
            'lsr_created_dt' => date('Y-m-d H:i:s'),
            'lsr_updated_dt' => date('Y-m-d H:i:s')
        ]);

        $this->insert('{{%lead_status_reason}}', [
            'lsr_key' => 'client_needs_no_sales',
            'lsr_name' => 'Client needs no Sales assistance',
            'lsr_description' => '',
            'lsr_enabled' => 1,
            'lsr_comment_required' => 0,
            'lsr_params' => json_encode([
                'count_to_conversion' => true,
                'double_resolution_principle' => true,
                'qa_test' => false
            ]),
            'lsr_created_user_id' => null,
            'lsr_updated_user_id' => null,
            'lsr_created_dt' => date('Y-m-d H:i:s'),
            'lsr_updated_dt' => date('Y-m-d H:i:s')
        ]);

        $this->insert('{{%lead_status_reason}}', [
            'lsr_key' => 'competitor_has_a_better_contract',
            'lsr_name' => 'Competitor has a better contract',
            'lsr_description' => '',
            'lsr_enabled' => 1,
            'lsr_comment_required' => 0,
            'lsr_params' => json_encode([
                'count_to_conversion' => true,
                'double_resolution_principle' => true,
                'qa_test' => false
            ]),
            'lsr_created_user_id' => null,
            'lsr_updated_user_id' => null,
            'lsr_created_dt' => date('Y-m-d H:i:s'),
            'lsr_updated_dt' => date('Y-m-d H:i:s')
        ]);

        $this->insert('{{%lead_status_reason}}', [
            'lsr_key' => 'duplicated',
            'lsr_name' => 'Duplicated',
            'lsr_description' => '',
            'lsr_enabled' => 1,
            'lsr_comment_required' => 1,
            'lsr_params' => json_encode([
                'count_to_conversion' => false,
                'double_resolution_principle' => false,
                'qa_test' => false
            ]),
            'lsr_created_user_id' => null,
            'lsr_updated_user_id' => null,
            'lsr_created_dt' => date('Y-m-d H:i:s'),
            'lsr_updated_dt' => date('Y-m-d H:i:s')
        ]);

        $this->insert('{{%lead_status_reason}}', [
            'lsr_key' => 'invalid',
            'lsr_name' => 'Invalid | Wrong Number | Spam',
            'lsr_description' => '',
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

        $this->insert('{{%lead_status_reason}}', [
            'lsr_key' => 'proper_follow_up_done',
            'lsr_name' => 'Proper Follow Up Done (No Answer)',
            'lsr_description' => '',
            'lsr_enabled' => 1,
            'lsr_comment_required' => 0,
            'lsr_params' => json_encode([
                'count_to_conversion' => true,
                'double_resolution_principle' => true,
                'qa_test' => false
            ]),
            'lsr_created_user_id' => null,
            'lsr_updated_user_id' => null,
            'lsr_created_dt' => date('Y-m-d H:i:s'),
            'lsr_updated_dt' => date('Y-m-d H:i:s')
        ]);

        $this->insert('{{%lead_status_reason}}', [
            'lsr_key' => 'purchased_elsewhere',
            'lsr_name' => 'Purchased Elsewhere',
            'lsr_description' => '',
            'lsr_enabled' => 1,
            'lsr_comment_required' => 0,
            'lsr_params' => json_encode([
                'count_to_conversion' => true,
                'double_resolution_principle' => true,
                'qa_test' => false
            ]),
            'lsr_created_user_id' => null,
            'lsr_updated_user_id' => null,
            'lsr_created_dt' => date('Y-m-d H:i:s'),
            'lsr_updated_dt' => date('Y-m-d H:i:s')
        ]);

        $this->insert('{{%lead_status_reason}}', [
            'lsr_key' => 'test',
            'lsr_name' => 'Test',
            'lsr_description' => '',
            'lsr_enabled' => 1,
            'lsr_comment_required' => 0,
            'lsr_params' => json_encode([
                'count_to_conversion' => false,
                'double_resolution_principle' => false,
                'qa_test' => false
            ]),
            'lsr_created_user_id' => null,
            'lsr_updated_user_id' => null,
            'lsr_created_dt' => date('Y-m-d H:i:s'),
            'lsr_updated_dt' => date('Y-m-d H:i:s')
        ]);

        $this->insert('{{%lead_status_reason}}', [
            'lsr_key' => 'transfer',
            'lsr_name' => 'Transfer (Customer Care | Exchange | Schedule Change | Sales agent)',
            'lsr_description' => '',
            'lsr_enabled' => 1,
            'lsr_comment_required' => 0,
            'lsr_params' => json_encode([
                'count_to_conversion' => false,
                'double_resolution_principle' => false,
                'qa_test' => true
            ]),
            'lsr_created_user_id' => null,
            'lsr_updated_user_id' => null,
            'lsr_created_dt' => date('Y-m-d H:i:s'),
            'lsr_updated_dt' => date('Y-m-d H:i:s')
        ]);

        $this->insert('{{%lead_status_reason}}', [
            'lsr_key' => 'travel_dates_passed',
            'lsr_name' => 'Travel dates passed',
            'lsr_description' => '',
            'lsr_enabled' => 1,
            'lsr_comment_required' => 0,
            'lsr_params' => json_encode([
                'count_to_conversion' => true,
                'double_resolution_principle' => true,
                'qa_test' => false
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
                'alternative',
                'booked_with_another_agent',
                'canceled_trip',
                'client_asked_not_to_be_contacted_again',
                'client_needs_no_sales',
                'competitor_has_a_better_contract',
                'duplicated',
                'invalid',
                'proper_follow_up_done',
                'purchased_elsewhere',
                'transfer',
                'travel_dates_passed',
                'test',
            ]
        ]);
        $this->alterColumn('{{%lead_status_reason}}', 'lsr_key', $this->string(30));
        $this->alterColumn('{{%lead_status_reason}}', 'lsr_name', $this->string(50));
    }
}
