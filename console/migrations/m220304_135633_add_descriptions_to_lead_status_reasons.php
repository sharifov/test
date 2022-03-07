<?php

use yii\db\Migration;

/**
 * Class m220304_135633_add_descriptions_to_lead_status_reasons
 */
class m220304_135633_add_descriptions_to_lead_status_reasons extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
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

        $this->alterColumn('{{%lead_status_reason}}', 'lsr_description', $this->text());
        $this->insert('{{%lead_status_reason}}', [
            'lsr_key' => 'alternative',
            'lsr_name' => 'Alternative',
            'lsr_description' => '- All the leads created to process an alternative. 
- Failed Bookings

NOTE:

- Should be an email with the respective Alternative, generated by the experts ;
- These requests will verified and confirmed by QA;

',
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
            'lsr_description' => 'Applies only to cases when the client has purchased the tickets with another agent from our Company.',
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
            'lsr_description' => 'Applies only to cases when the client is cancelling the Trip.',
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
            'lsr_description' => 'Applies to cases only, when clients requested NOT to be contacted again by email/phone. 

Agent must send an email request to QA, in order to unsubscribe the client.',
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
            'lsr_description' => 'Applies to cases:

- When the client is no longer interested in our services;
- When the client says that is not interested in any sales assistance;
- General Question (Ex: "COVID Policy" )',
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
            'lsr_description' => 'These are the requests, where clients found lower prices Online and we are not able to match them. Agent must make sure that the fare, found by the client Online can be booked. Such requests will go to FollowUP Queue.

NOTE:

Agent must register the following information on Competitor price in Comments box:
- Airline Name;
- Total Price per passenger;',
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
            'lsr_description' => 'Applies only to Leads: 

- There is a processing Lead under the same customer;',
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
            'lsr_description' => 'Applies to the following scenarios:

- When the client confirms that this is a wrong number (in case there is no email);
- When the client intended to call a different company;
- When the number is invalid;',
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
            'lsr_description' => 'Only applies to the following Leads:

1) The client responded to the 1st call and confirmed the flight request, but no answer after initial call.

2) Agent must make outbound calls for 2 days. 1st day (initial call + 2 calls + 3 PQs (emails/SMS) or 1 PQ (if its rush or specific). 2nd day  (2 calls + 1 PQ (emails/SMS) + FU SMS/email (optional)).
',
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
            'lsr_description' => 'Applies only to cases when the client says, that already purchased his/her tickets elsewhere.',
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
            'lsr_description' => 'Test Leads that do not have any connection with real clients. 

NOTE: 
- If the Lead is confirmed not to be a Test one, a penalty of $10 NET is applied.',
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
            'lsr_description' => 'All the leads that do not require sales departmentt assistance;

Agents must distribute all this kind of calls in the following way:

    1) ​If ​customer wants to make ​any changes in the existing reservation​.
"(ex. Change the departure/Return date, flight time is not convenient, change in plans and need adjustments etc. )"
---> ​transfer to Exchange line​:
    2) ​If ​customer has ​verification​ related​ questions.
​"(ex. small amounts on hold , CC declined, double charge etc.)"
---> transfer to Fraud Prevention line​ :
    3) If the customer needs a refund/exchange.
---> transfer to Exchange Team.

NOTE: 
- If the agent provides the PRICELINE Customer Care number (+1 877 477 5807), the reason must be "Transfer to Customer Care".
- In case a sales Agent who has access to Exchange helps the customer and has an active case with this customer trashes the lead as "Transfer to Exchange", it is considered as True. 
- If the agent answers Exchange or Customer Care-related questions and does not transfer the call to another department, this reason is true. 
- If the client is looking for another agent from sales, the Lead is trashed as Transfer to Sales. ',
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
            'lsr_description' => 'The flight request travel dates passed.',
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
    }
}
