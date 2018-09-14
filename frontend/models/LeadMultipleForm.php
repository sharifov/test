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

    public $reason_id;
    public $reason_description;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lead_list'], 'required'],
            [['employee_id', 'status_id', 'rating', 'reason_id'], 'integer'],
            [['reason_description'], 'string'],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['employee_id' => 'id'], 'when' => function (self $model) {
                return $model->employee_id > 0;
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lead_list'             => 'Selected Leads',
            'employee_id'           => 'Employee',
            'status_id'             => 'Send To',
            'rating'                => 'Rating',
            'reason_id'             => 'Reason',
            'reason_description'    => 'Reason Description',
        ];
    }
}
