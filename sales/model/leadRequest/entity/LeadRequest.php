<?php

namespace sales\model\leadRequest\entity;

use common\components\validators\CheckJsonValidator;
use sales\entities\EventTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "lead_request".
 *
 * @property int $lr_id
 * @property string $lr_type
 * @property int|null $lr_job_id
 * @property string|null $lr_json_data
 * @property string|null $lr_created_dt
 */
class LeadRequest extends \yii\db\ActiveRecord
{
    use EventTrait;

    public const TYPE_GOOGLE = 'google';

    public const TYPE_LIST = [
        self::TYPE_GOOGLE => 'Google'
    ];

    public function rules(): array
    {
        return [
            ['lr_type', 'required'],
            ['lr_type', 'string', 'max' => 50],
            ['lr_type', 'in', 'range' => array_keys(self::TYPE_LIST)],

            ['lr_job_id', 'integer'],

            ['lr_json_data', CheckJsonValidator::class, 'skipOnEmpty' => true],

            ['lr_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['lr_created_dt', 'lr_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['lr_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function attributeLabels(): array
    {
        return [
            'lr_id' => 'ID',
            'lr_type' => 'Type',
            'lr_job_id' => 'Job ID',
            'lr_json_data' => 'Json Data',
            'lr_created_dt' => 'Created Dt',
        ];
    }

    public static function find(): LeadRequestScopes
    {
        return new LeadRequestScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'lead_request';
    }
}
