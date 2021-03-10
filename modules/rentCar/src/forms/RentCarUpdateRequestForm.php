<?php

namespace modules\rentCar\src\forms;

use DateTime;
use modules\rentCar\src\entity\rentCar\RentCar;
use modules\rentCar\src\helpers\RentCarHelper;
use yii\base\Model;

/**
 * Class RentCarUpdateRequestForm
 *
 * @property string|null $pick_up_code
 * @property string|null $drop_off_code
 * @property string|null $pick_up_date
 * @property string|null $drop_off_date
 * @property string|null $pick_up_time
 * @property string|null $drop_off_time
 */
class RentCarUpdateRequestForm extends Model
{
    public $pick_up_code;
    public $drop_off_code;
    public $pick_up_date;
    public $drop_off_date;
    public $pick_up_time;
    public $drop_off_time;

    public function __construct(RentCar $rentCar, $config = [])
    {
        parent::__construct($config);

        $this->pick_up_code = $rentCar->prc_pick_up_code;
        $this->pick_up_date = $rentCar->prc_pick_up_date;
        $this->drop_off_code = $rentCar->prc_drop_off_code;
        $this->drop_off_date = $rentCar->prc_drop_off_date;
        $this->pick_up_time = $rentCar->prc_pick_up_time ? (string) date('H:i', strtotime($rentCar->prc_pick_up_time)) : RentCarHelper::DEFAULT_TIME;
        $this->drop_off_time = $rentCar->prc_drop_off_time ? (string) date('H:i', strtotime($rentCar->prc_drop_off_time)) : RentCarHelper::DEFAULT_TIME;
    }

    public function rules(): array
    {
        return [
            [['pick_up_code', 'pick_up_date', 'drop_off_date'], 'required'],

            [['pick_up_code', 'drop_off_code'], 'string', 'max' => 10],

            [['pick_up_date', 'drop_off_date'], 'filter', 'filter' => static function ($value) {
                return date('Y-m-d', strtotime($value));
            }],
            [['pick_up_date', 'drop_off_date'], 'date', 'format' => 'php:Y-m-d'],

            [['pick_up_time', 'drop_off_time'], 'date', 'format' => 'php:H:i'],

            ['drop_off_date', 'validateDateTime'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'pick_up_code' => 'Pick up',
            'drop_off_code' => 'Drop off',
            'pick_up_date' => 'Pick up Date',
            'drop_off_date' => 'Drop off Data',
        ];
    }

    public function validateDateTime($attribute, $params, $validator): void
    {
        $pickUpSource = $this->pick_up_date . ' ' . $this->pick_up_time;
        $pickUpDt = DateTime::createFromFormat(RentCar::FORMAT_DT, $pickUpSource);

        $dropOffSource = $this->drop_off_date . ' ' . $this->drop_off_time;
        $dropOffDt = DateTime::createFromFormat(RentCar::FORMAT_DT, $dropOffSource);

        if ($pickUpDt > $dropOffDt) {
            $this->addError($attribute, 'DropOff date/time must be younger PickUp');
        }
    }
}
