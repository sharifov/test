<?php

namespace frontend\models;

use common\models\Employee;
use yii\base\Model;

/**
 * LeadMultipleForm form
 */
class LeadMultipleForm extends Model
{
    public $lead_list;
    public $employee_id;
    public $status_id;
    public $rating;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lead_list'], 'required'],
            [['employee_id', 'status_id', 'rating'], 'integer'],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['employee_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lead_list'     => 'Selected Leads',
            'employee_id'   => 'Employee',
            'status_id'     => 'Status',
            'rating'        => 'Rating',
        ];
    }
}
