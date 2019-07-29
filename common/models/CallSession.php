<?php

namespace common\models;

use sales\entities\AggregateRoot;
use sales\entities\EventTrait;
use Yii;

/**
 * This is the model class for table "call_session".
 *
 * @property int $cs_id
 * @property int $cs_call_id
 * @property string $cs_cid
 * @property int $cs_step
 * @property int $cs_project_id
 * @property int $cs_lang_id
 * @property string $cs_data_params
 * @property string $cs_create_dt
 * @property string $cs_updated_dt
 */
class CallSession extends \yii\db\ActiveRecord implements AggregateRoot
{

    use EventTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'call_session';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cs_call_id', 'cs_data_params'], 'required'],
            [['cs_id', 'cs_call_id', 'cs_step', 'cs_project_id', 'cs_lang_id'], 'integer'],
            [['cs_data_params'], 'string'],
            [['cs_create_dt', 'cs_updated_dt'], 'safe'],
            [['cs_cid'], 'string', 'max' => 255],
            [['cs_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cs_id' => 'Cs ID',
            'cs_call_id' => 'Cs Call ID',
            'cs_cid' => 'Cs Cid',
            'cs_step' => 'Cs Step',
            'cs_data_params' => 'Cs Data Params',
            'cs_create_dt' => 'Cs Create Dt',
            'cs_updated_dt' => 'Cs Updated Dt',
        ];
    }

    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {

            if($insert) {
                if(!$this->cs_create_dt) {
                    $this->cs_create_dt = date('Y-m-d H:i:s');
                }
            }

            $this->cs_updated_dt = date('Y-m-d H:i:s');
            return true;
        }
        return false;
    }
}
