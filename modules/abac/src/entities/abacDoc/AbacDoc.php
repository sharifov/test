<?php

namespace modules\abac\src\entities\abacDoc;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "abac_doc".
 *
 * @property int $ad_id
 * @property string|null $ad_file
 * @property int|null $ad_line
 * @property string|null $ad_subject
 * @property string|null $ad_object
 * @property string|null $ad_action
 * @property string|null $ad_description
 * @property string|null $ad_created_dt
 */
class AbacDoc extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            [['ad_file', 'ad_line'], 'required'],

            ['ad_action', 'string', 'max' => 50],
            ['ad_description', 'string', 'max' => 50],
            ['ad_object', 'string', 'max' => 50],
            ['ad_subject', 'string', 'max' => 50],

            ['ad_file', 'string', 'max' => 100],

            ['ad_line', 'integer'],

            ['ad_created_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'ad_id' => 'ID',
            'ad_file' => 'File',
            'ad_line' => 'Line',
            'ad_subject' => 'Subject',
            'ad_object' => 'Object',
            'ad_action' => 'Action',
            'ad_description' => 'Description',
            'ad_created_dt' => 'Created Dt',
        ];
    }

    public static function find(): AbacDocScopes
    {
        return new AbacDocScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'abac_doc';
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ad_created_dt', 'ad_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ad_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ]
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }
}
