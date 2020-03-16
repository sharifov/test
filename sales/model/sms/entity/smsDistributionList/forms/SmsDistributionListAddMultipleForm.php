<?php

namespace sales\model\sms\entity\smsDistributionList\forms;
use borales\extensions\phoneInput\PhoneInputValidator;
use yii\base\Model;

/**
 * This is the form model class for table "sms_distribution_list".
 *
 * @property int $sdl_project_id
 * @property string $sdl_phone_from
 * @property string $sdl_phone_to_list
 * @property string $sdl_text
 * @property string|null $sdl_start_dt
 * @property string|null $sdl_end_dt
 * @property int|null $sdl_status_id
 * @property int|null $sdl_priority
 *
 */
class SmsDistributionListAddMultipleForm extends Model
{

    public $sdl_project_id;
    public $sdl_phone_from;
    public $sdl_phone_to_list;
    public $sdl_text;
    public $sdl_start_dt;
    public $sdl_end_dt;
    public $sdl_status_id;
    public $sdl_priority;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['sdl_status_id', 'sdl_priority', 'sdl_project_id'], 'integer'],
            [['sdl_phone_from', 'sdl_phone_to_list', 'sdl_text', 'sdl_status_id', 'sdl_project_id'], 'required'],
            [['sdl_phone_from'], PhoneInputValidator::class],

            [['sdl_phone_to_list'], 'filter', 'filter' => static function ($value){
                return str_replace(['(', ')', '-', ' '], '', $value);
            }],

            [['sdl_text', 'sdl_phone_to_list'], 'string'],
            [['sdl_start_dt', 'sdl_end_dt'], 'safe'],
            [['sdl_phone_from'], 'string', 'max' => 20],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'sdl_project_id' => 'Project ID',
            'sdl_phone_from' => 'Phone From',
            'sdl_phone_to' => 'Phones To List',
            'sdl_text' => 'Text',
            'sdl_start_dt' => 'Start Date',
            'sdl_end_dt' => 'End Date',
            'sdl_status_id' => 'Status',
            'sdl_priority' => 'Priority',
            'sdl_phone_to_list' => 'Phone To List'
        ];
    }
}
