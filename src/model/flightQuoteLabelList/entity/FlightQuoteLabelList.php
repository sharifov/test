<?php

namespace src\model\flightQuoteLabelList\entity;

use common\models\Employee;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "flight_quote_label_list".
 *
 * @property int $fqll_id
 * @property string $fqll_label_key
 * @property string|null $fqll_origin_description
 * @property string|null $fqll_description
 * @property string|null $fqll_created_dt
 * @property string|null $fqll_updated_dt
 * @property int|null $fqll_created_user_id
 * @property int|null $fqll_updated_user_id
 *
 * @property Employee $fqllCreatedUser
 * @property Employee $fqllUpdatedUser
 */
class FlightQuoteLabelList extends \yii\db\ActiveRecord
{
    public function rules(): array
    {
        return [
            ['fqll_label_key', 'required'],
            ['fqll_label_key', 'string', 'max' => 50],
            ['fqll_label_key', 'unique'],

            ['fqll_origin_description', 'string', 'max' => 255],
            ['fqll_description', 'string', 'max' => 255],

            [['fqll_created_dt', 'fqll_updated_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            [['fqll_created_user_id', 'fqll_updated_user_id'], 'integer'],
            [['fqll_created_user_id', 'fqll_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['fqll_updated_user_id' => 'id']],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['fqll_created_dt', 'fqll_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['fqll_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['fqll_created_user_id', 'fqll_updated_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['fqll_updated_user_id'],
                ],
                'defaultValue' => null
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getFqllCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'fqll_created_user_id']);
    }

    public function getFqllUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'fqll_updated_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'fqll_id' => 'ID',
            'fqll_label_key' => 'Label Key',
            'fqll_origin_description' => 'Origin Description',
            'fqll_description' => 'Description',
            'fqll_created_dt' => 'Created Dt',
            'fqll_updated_dt' => 'Updated Dt',
            'fqll_created_user_id' => 'Created User ID',
            'fqll_updated_user_id' => 'Updated User ID',
        ];
    }

    public static function find(): FlightQuoteLabelListScopes
    {
        return new FlightQuoteLabelListScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'flight_quote_label_list';
    }

    public static function getDescriptionByKey(?string $key, int $duration = 60): string
    {
        if (empty($key) || !is_string($key)) {
            return '';
        }

        return Yii::$app->cache->getOrSet('flight_quote_label_list-' . $key, static function () use ($key) {
            if ($description = self::find()->select('fqll_description')->where(['fqll_label_key' => $key])->scalar()) {
                return $description;
            }
            if ($description = self::find()->select('fqll_origin_description')->where(['fqll_label_key' => $key])->scalar()) {
                return $description;
            }
            return $key;
        }, $duration, new TagDependency([
            'tags' => 'flight_quote_label_list-' . $key,
        ]));
    }
}
