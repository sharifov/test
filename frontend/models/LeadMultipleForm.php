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
 * @property int $redial_queue
 */
class LeadMultipleForm extends Model
{
    public const REDIAL_ADD = 1;
    public const REDIAL_REMOVE = 2;

    public const REDIAL_QUEUE_LIST = [
        self::REDIAL_ADD => 'Add to Redial Queue',
        self::REDIAL_REMOVE => 'Remove from Redial Queue',
    ];

    public $lead_list;
    public $employee_id;
    public $status_id;
    public $rating;

    public $redial_queue;

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

            ['status_id', 'integer'],

            ['rating', 'integer'],

            ['reason_id', 'integer'],

            ['reason_description', 'string'],

            ['employee_id', 'integer'],
            ['employee_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['employee_id' => 'id'], 'when' => function (self $model) {
                return $model->employee_id > 0;
            }],
            ['employee_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['employee_id', 'default', 'value' => null],

            ['redial_queue', 'in', 'range' => array_keys(self::REDIAL_QUEUE_LIST)],
            ['redial_queue', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
        ];
    }

    public function isRedialProcess(): bool
    {
        return array_key_exists($this->redial_queue, self::REDIAL_QUEUE_LIST);
    }

    public function isRedialAdd(): bool
    {
        return $this->redial_queue === self::REDIAL_ADD;
    }

    public function isRedialRemove(): bool
    {
        return $this->redial_queue === self::REDIAL_REMOVE;
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
