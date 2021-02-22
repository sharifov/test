<?php

namespace modules\rentCar\src\forms;

use modules\rentCar\src\entity\rentCar\RentCar;
use yii\base\Model;

/**
 * Class RentCarSearchForm
 *
 * @property string|null $prc_pick_up_code
 * @property string|null $prc_drop_off_code
 * @property string|null $prc_pick_up_date
 * @property string|null $prc_drop_off_date
 * @property string|null $prc_pick_up_time
 * @property string|null $prc_drop_off_time
 */
class RentCarSearchForm extends Model
{
    public $prc_pick_up_code;
    public $prc_drop_off_code;
    public $prc_pick_up_date;
    public $prc_drop_off_date;
    public $prc_pick_up_time;
    public $prc_drop_off_time;

    public function __construct(RentCar $rentCar, $config = [])
    {
        parent::__construct($config);

        $this->prc_pick_up_code = $rentCar->prc_pick_up_code;
        $this->prc_drop_off_code = $rentCar->prc_drop_off_code;
        $this->prc_pick_up_date = $rentCar->prc_pick_up_date;
        $this->prc_drop_off_date = $rentCar->prc_drop_off_date;
        $this->prc_pick_up_time = (string) date('H:i', strtotime($rentCar->prc_pick_up_time));
        $this->prc_drop_off_time = (string) date('H:i', strtotime($rentCar->prc_drop_off_time));
    }

    public function rules(): array
    {
        return [
            [['prc_pick_up_code', 'prc_pick_up_date', 'prc_drop_off_date'], 'required'],

            [['prc_pick_up_code', 'prc_drop_off_code'], 'string', 'max' => 10],

            [['prc_pick_up_date', 'prc_drop_off_date'], 'filter', 'filter' => static function ($value) {
                return date('Y-m-d', strtotime($value));
            }],
            [['prc_pick_up_date', 'prc_drop_off_date'], 'date', 'format' => 'php:Y-m-d'],

            [['prc_pick_up_time', 'prc_drop_off_time'], 'date', 'format' => 'php:H:i'],
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
}
