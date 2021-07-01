<?php

namespace sales\model\contactPhoneData\entity;

use sales\model\contactPhoneData\service\ContactPhoneDataDictionary;
use sales\model\contactPhoneList\entity\ContactPhoneList;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "contact_phone_data".
 *
 * @property int $cpd_cpl_id
 * @property string $cpd_key
 * @property string $cpd_value
 * @property string|null $cpd_created_dt
 * @property string|null $cpd_updated_dt
 *
 * @property ContactPhoneList $cpdCpl
 */
class ContactPhoneData extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [['cpd_cpl_id', 'cpd_key'], 'unique', 'targetAttribute' => ['cpd_cpl_id', 'cpd_key']],

            ['cpd_cpl_id', 'required'],
            ['cpd_cpl_id', 'integer'],
            ['cpd_cpl_id', 'exist', 'skipOnError' => true, 'targetClass' => ContactPhoneList::class, 'targetAttribute' => ['cpd_cpl_id' => 'cpl_id']],

            ['cpd_key', 'required'],
            ['cpd_key', 'string', 'max' => 30],
            [['cpd_key'], 'in', 'range' => ContactPhoneDataDictionary::KEY_LIST],

            ['cpd_value', 'required'],
            ['cpd_value', 'string', 'max' => 100],

            [['cpd_created_dt', 'cpd_updated_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cpd_created_dt', 'cpd_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['cpd_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getCpdCpl(): \yii\db\ActiveQuery
    {
        return $this->hasOne(ContactPhoneList::class, ['cpl_id' => 'cpd_cpl_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'cpd_cpl_id' => 'Cpl ID',
            'cpd_key' => 'Key',
            'cpd_value' => 'Value',
            'cpd_created_dt' => 'Created',
            'cpd_updated_dt' => 'Updatedt',
        ];
    }

    public static function find(): ContactPhoneDataScopes
    {
        return new ContactPhoneDataScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'contact_phone_data';
    }

    /**
     * @param int $cplId
     * @param $key
     * @param $value
     * @return ContactPhoneData
     */
    public static function create(int $cplId, $key, $value): ContactPhoneData
    {
        $model = new self();
        $model->cpd_cpl_id = $cplId;
        $model->cpd_key = $key;
        $model->cpd_value = $value;
        return $model;
    }
}
