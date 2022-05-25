<?php

namespace modules\shiftSchedule\src\forms;

use yii\base\Model;

class ScheduleDecisionForm extends Model
{
    const DESCRIPTION_MAX_LENGTH = 1000;

    /**
     * @var string
     */
    public string $description = '';
    /**
     * @var int
     */
    public int $status = 0;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [
                'status',
                'required',
            ],
            [
                'description',
                'required',
            ],
            [
                'description',
                'string',
                'max' => self::DESCRIPTION_MAX_LENGTH,
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'status' => 'Status',
            'description' => 'Description',
        ];
    }
}
