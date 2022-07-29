<?php

use yii\db\Migration;

/**
 * Class m220727_052219_change_closing_reason
 */
class m220727_052219_change_closing_reason extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update(
            '{{%lead_status_reason}}',
            [
                'lsr_name' => 'Client needs no Sales assistance (only first call/email/sms)',
                'lsr_description' => 'Applies to first call/email/sms cases only, when an agent finds out, that the client does not require the Sales Floor assistance. This includes:

- The client purchased tickets elsewhere before agent got in touch with him;
- Either one of the requested travel dates is more than 13 months ahead;
- The client is no longer interested in our services by the time an agent gets in touch with him;
- General Question (Ex: "COVID Policy" )',
                'lsr_params' => json_encode([
                    'count_to_conversion' => false,
                    'double_resolution_principle' => false,
                    'qa_test' => true
                ]),

            ],
            ['lsr_key' => 'client_needs_no_sales']
        );

        $this->update(
            '{{%lead_status_reason}}',
            [
                'lsr_description' => 'The system will allow closing requests as Duplicate if the following conditions are met:

- Original request is created for the same client;
- Duplicate request has no price quotes sent to the client;
- Original and Duplicate requests are created within last 30 days;
- Original request has no sales created more than 7 days ago;
- There is no Split Remark in Duplicate Request.',
                'lsr_params' => json_encode([
                    'count_to_conversion' => false,
                    'double_resolution_principle' => false,
                    'qa_test' => true
                ]),

            ],
            ['lsr_key' => 'duplicated']
        );

        $this->update(
            '{{%lead_status_reason}}',
            [
                'lsr_params' => json_encode([
                    'count_to_conversion' => false,
                    'double_resolution_principle' => false,
                    'qa_test' => true
                ]),

            ],
            ['lsr_key' => 'invalid']
        );

        $this->update(
            '{{%lead_status_reason}}',
            [
                'lsr_name' => 'Proper Follow Up Done (Client answered)',
                'lsr_description' => 'Only applies to the following Leads:

1) The client responded to at least one call / email / sms.

2) Agent has to complete the minimum required follow up of 3 days. 
1st day (initial call + 2 calls + 3 PQs (emails/SMS) or 1 PQ (if it\'s rush or specific). 
2nd day  (2 calls + 1 PQ (emails/SMS) + FU SMS/email (optional)). 
3rd day  (2 calls + 1 PQ (emails/SMS) + FU SMS/email (optional)).
',
            ],
            ['lsr_key' => 'proper_follow_up_done']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->update(
            '{{%lead_status_reason}}',
            [
                'lsr_name' => 'Client needs no Sales assistance',
                'lsr_description' => 'Applies to cases:

- When the client is no longer interested in our services;
- When the client says that is not interested in any sales assistance;
- General Question (Ex: "COVID Policy" )',
                'lsr_params' => json_encode([
                    'count_to_conversion' => true,
                    'double_resolution_principle' => true,
                    'qa_test' => false
                ]),

            ],
            ['lsr_key' => 'client_needs_no_sales']
        );

        $this->update(
            '{{%lead_status_reason}}',
            [
                'lsr_description' => 'Applies only to Leads: 

- There is a processing Lead under the same customer;',
                'lsr_params' => json_encode([
                    'count_to_conversion' => false,
                    'double_resolution_principle' => false,
                    'qa_test' => false
                ]),

            ],
            ['lsr_key' => 'duplicated']
        );

        $this->update(
            '{{%lead_status_reason}}',
            [
                'lsr_params' => json_encode([
                    'count_to_conversion' => false,
                    'double_resolution_principle' => true,
                    'qa_test' => true
                ]),

            ],
            ['lsr_key' => 'invalid']
        );

        $this->update(
            '{{%lead_status_reason}}',
            [
                'lsr_name' => 'Proper Follow Up Done (No Answer)',
                'lsr_description' => 'Only applies to the following Leads:

1) The client responded to the 1st call and confirmed the flight request, but no answer after initial call.

2) Agent must make outbound calls for 2 days. 1st day (initial call + 2 calls + 3 PQs (emails/SMS) or 1 PQ (if its rush or specific). 2nd day  (2 calls + 1 PQ (emails/SMS) + FU SMS/email (optional)).
',
            ],
            ['lsr_key' => 'proper_follow_up_done']
        );
    }
}
