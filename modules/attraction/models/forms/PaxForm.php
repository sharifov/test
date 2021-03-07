<?php

namespace modules\attraction\models\forms;

use modules\attraction\models\Attraction;
use Yii;
use yii\base\Model;
use yii\helpers\VarDumper;
use yii\validators\RequiredValidator;

/**
 * This is the model class for table "attraction".
 *
 * @property int $atn_attraction_id
 * @property int $product_id
 * @property array|null $atn_pax_list
 *
 */
class PaxForm extends Model
{

    public $atn_attraction_id;
    public $product_id;
    public $atn_pax_list;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['atn_attraction_id'], 'required'],
            [['atn_attraction_id', 'product_id'], 'integer'],
            [['atn_pax_list'], 'validatePaxList' /*, 'skipOnEmpty' => false*/],
            [['atn_attraction_id'], 'exist', 'skipOnError' => true, 'targetClass' => Attraction::class, 'targetAttribute' => ['atn_attraction_id' => 'atn_id']],
        ];
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validatePaxList($attribute, $params): void
    {
        if (empty($this->atn_pax_list)) {
            $this->addError('atn_pax_list', 'Pax list cannot be empty');
        } elseif (!is_array($this->atn_pax_list)) {
            $this->addError('atn_pax_list', 'Pax list must be array');
        } else {
            //$dataErrors = [];
            foreach ($this->atn_pax_list as $nr => $paxData) {
                $model = new AttractionPaxForm();
                $model->attributes = $paxData;

                if (!$model->validate()) {
                    if ($model->errors) {
                        //VarDumper::dump($model->errors); //exit;
                        foreach ($model->errors as $keyError => $error) {
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
            'atn_attraction_id' => 'Hotel ID',
            'atn_pax_list'   => 'Pax list'
        ];
    }
}
