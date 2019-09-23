<?php

namespace frontend\models;

use common\models\Employee;
use yii\base\Model;

/**
 * LeadMultipleForm form
 *
 * @property array $lead_list
 * @property int $employee_id
 * @property int $status_id
 * @property int $rating
 * @property int $reason_id
 * @property string $reason_description
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
     * @return array
     */
    public function rules(): array
    {
        return [
            ['lead_list', 'required'],
            ['lead_list', 'leadListValidate'],

            ['employee_id', 'integer'],

            ['status_id', 'integer'],

            ['rating', 'integer'],

            ['reason_id', 'integer'],

            ['reason_description', 'string'],

            ['employee_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['employee_id' => 'id'], 'when' => function (self $model) {
                return $model->employee_id > 0;
            }],
        ];
    }

    public function leadListValidate(): void
    {
        if (!is_array($this->lead_list)) {
            $this->addError('lead_list', 'Error format lead list');
        }
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
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
