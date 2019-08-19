<?php

use yii\db\Migration;

/**
 * Class m190819_134505_insert_case_categories
 */
class m190819_134505_insert_case_categories extends Migration
{

    public $categoryList = [
        ['cc_key' => 'exchange', 'cc_name' => 'Exchange', 'cc_dep_id' =>  2, 'cc_system' => false],
        ['cc_key' => 'refund', 'cc_name' => 'Refund', 'cc_dep_id' =>  2, 'cc_system' => false],
        ['cc_key' => 'name_correction', 'cc_name' => 'Name correction', 'cc_dep_id' =>  2, 'cc_system' => false],
        ['cc_key' => 'cancellation_credit', 'cc_name' => 'Cancellation with credit', 'cc_dep_id' =>  2, 'cc_system' => false],
        ['cc_key' => 'waiver_email', 'cc_name' => 'Waiver (Email)', 'cc_dep_id' =>  2, 'cc_system' => false],
        ['cc_key' => 'waiver_phone', 'cc_name' => 'Waiver (Phone)', 'cc_dep_id' =>  2, 'cc_system' => false],
        ['cc_key' => 'other_faq', 'cc_name' => 'Other FAQ', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'verify_questions', 'cc_name' => 'Verify questions', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'ticket_receipt', 'cc_name' => 'Ticket receipt', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'cancel_not_issued', 'cc_name' => 'Cancel, not issued', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'void_request', 'cc_name' => 'Void request', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'seat_assignment', 'cc_name' => 'Seat assignment', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'website_issues', 'cc_name' => 'Website issues', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'card_declined', 'cc_name' => 'Card declined', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'flight_unclear', 'cc_name' => 'Flight details unclear', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'cancellation', 'cc_name' => 'Cancellation', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'meals_wheelchair_ff', 'cc_name' => 'Meals/wheelchair/FF', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'adjust_dob', 'cc_name' => 'Adjust DOB', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'transfer', 'cc_name' => 'Transfer', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'airport_check-in_issues', 'cc_name' => 'Airport/Check-in issues', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'add_infant', 'cc_name' => 'Add infant', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'double_wrong_charges', 'cc_name' => 'Double/wrong charges', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'add_insurance', 'cc_name' => 'Add insurance', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'upgrade_seats', 'cc_name' => 'Upgrade Seats', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'inaccurate_prices', 'cc_name' => 'Inaccurate prices', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'wrong_name', 'cc_name' => 'Wrong name, not issued', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'add_passengers', 'cc_name' => 'Add passengers', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'add_unmr__pets', 'cc_name' => 'Add UNMR or pets', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'cancel_insurance', 'cc_name' => 'Cancel insurance', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'chargeback', 'cc_name' => 'Chargeback', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'airport_transfer_issue', 'cc_name' => 'Airport transfer issue', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'wrong_email', 'cc_name' => 'Wrong email', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'flight_not_available', 'cc_name' => 'Flight not available', 'cc_dep_id' =>  3, 'cc_system' => false],
        ['cc_key' => 'schg_case', 'cc_name' => 'SCHG case', 'cc_dep_id' =>  3, 'cc_system' => false],
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->truncateTable('{{%cases_category}}');

        foreach ($this->categoryList as $item) {
            $item['cc_created_dt'] = date('Y-m-d H:i:s');
            $this->insert('{{%cases_category}}', $item);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        foreach ($this->categoryList as $item) {
            $this->delete('{{%cases_category}}', ['cc_key' => $item['cc_key']]);
        }
    }


}
