<?php

namespace src\model\leadDataKey\entity;

use common\models\Employee;
use src\behaviors\cache\CleanCacheBehavior;
use src\behaviors\KeySlugBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\caching\TagDependency;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "lead_data_key".
 *
 * @property int $ldk_id
 * @property string $ldk_key
 * @property string $ldk_name
 * @property int|null $ldk_enable
 * @property string|null $ldk_created_dt
 * @property string|null $ldk_updated_dt
 * @property int|null $ldk_created_user_id
 * @property int|null $ldk_updated_user_id
 * @property bool|null $ldk_is_system
 *
 * @property Employee $ldkCreatedUser
 * @property Employee $ldkUpdatedUser
 */
class LeadDataKey extends ActiveRecord
{
    public const CACHE_TAG = 'lead-data-key-tag-dependency';

    public function rules(): array
    {
        return [
            ['ldk_key', 'required'],
            ['ldk_key', 'string', 'max' => 50],
            ['ldk_key', 'unique'],

            ['ldk_name', 'required'],
            ['ldk_name', 'string', 'max' => 50],

            ['ldk_enable', 'boolean'],
            ['ldk_enable', 'default', 'value' => true],

            [['ldk_created_user_id', 'ldk_updated_user_id'], 'integer'],
            [['ldk_created_user_id', 'ldk_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ldk_updated_user_id' => 'id']],

            [['ldk_created_dt', 'ldk_updated_dt'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['ldk_is_system', 'boolean'],
            ['ldk_is_system', 'default', 'value' => false],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ldk_created_dt', 'ldk_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ldk_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ldk_created_user_id', 'ldk_updated_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ldk_updated_user_id'],
                ],
                'defaultValue' => null,
            ],
            'keySlug' => [
                'class' => KeySlugBehavior::class,
                'donorColumn' => 'ldk_key',
                'targetColumn' => 'ldk_key',
            ],
            'cleanCache' => [
                'class' => CleanCacheBehavior::class,
                'tags' => [self::CACHE_TAG],
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getLdkCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ldk_created_user_id']);
    }

    public function getLdkUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ldk_updated_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ldk_id' => 'ID',
            'ldk_key' => 'Key',
            'ldk_name' => 'Name',
            'ldk_enable' => 'Enable',
            'ldk_created_dt' => 'Created Dt',
            'ldk_updated_dt' => 'Updated Dt',
            'ldk_created_user_id' => 'Created User ID',
            'ldk_updated_user_id' => 'Updated User ID',
            'ldk_is_system' => 'Is System',
        ];
    }

    public static function find(): LeadDataKeyScopes
    {
        return new LeadDataKeyScopes(static::class);
    }

    public static function tableName(): string
    {
        return 'lead_data_key';
    }

    public static function getList(?bool $isEnable = true): array
    {
        $query = self::find()->select(['ldk_key', 'ldk_name']);
        if (is_bool($isEnable)) {
            $query->where(['ldk_enable' => $isEnable]);
        }
        return ArrayHelper::map(
            $query->all(),
            'ldk_key',
            'ldk_name'
        );
    }

    public static function getListCache(?bool $isEnable = true, int $duration = 60 * 60): array
    {
        return Yii::$app->cache->getOrSet(self::CACHE_TAG, static function () use ($isEnable) {
            return self::getList($isEnable);
        }, $duration, new TagDependency([
            'tags' => self::CACHE_TAG,
        ]));
    }

    public static function getKeyName(string $key): string
    {
        return ArrayHelper::getValue(self::getListCache(), $key, '-');
    }
}
