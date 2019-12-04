<?php

namespace modules\hotel\models\forms;

use modules\hotel\models\Hotel;
use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;
use yii\validators\RequiredValidator;

/**
 * This is the model class for table "hotel_room".
 *
 * @property int $hr_id
 * @property int $hr_hotel_id
 * @property string|null $hr_room_name
 * @property array|null $hr_pax_list
 *
 */
class HotelRoomForm extends Model
{

    public $hr_id;
    public $hr_hotel_id;
    public $hr_room_name;
    public $hr_pax_list;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['hr_hotel_id'], 'required'],
            [['hr_hotel_id'], 'integer'],
            [['hr_room_name'], 'string', 'max' => 200],
            [['hr_pax_list'], 'validatePaxList'/*, 'skipOnEmpty' => false*/],
            [['hr_hotel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Hotel::class, 'targetAttribute' => ['hr_hotel_id' => 'ph_id']],
        ];
    }


    /**
     * @param $attribute
     * @param $params
     */
    public function validatePaxList($attribute, $params): void
    {
        if (empty($this->hr_pax_list)) {
            $this->addError('hr_pax_list', 'Pax list cannot be empty');
        } elseif (!is_array($this->hr_pax_list)) {
            $this->addError('hr_pax_list', 'Pax list must be array');
        } else {
            //$dataErrors = [];
            foreach ($this->hr_pax_list as $nr => $paxData) {

                $model = new HotelRoomPaxForm();
                $model->attributes = $paxData;

                if (!$model->validate()) {
                    if ($model->errors) {
                        //VarDumper::dump($model->errors); //exit;
                        foreach ($model->errors as $keyError =>  $error) {
                            $errorValue = $error[0];
                            $key = $attribute . '[' . $nr . '][' . $keyError . ']';
                            $this->addError($key, 'Pax ' . ($nr + 1) . ': ' . $errorValue);
                        }
                    }

                    // $dataErrors [$nr] = $model->errors;
                }
            }

            //VarDumper::dump($dataErrors, 10, true); //exit;
        }
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'hr_id' => 'ID',
            'hr_hotel_id' => 'Hotel ID',
            'hr_room_name' => 'Room Name',
            'hr_pax_list'   => 'Pax list'
        ];
    }

}
