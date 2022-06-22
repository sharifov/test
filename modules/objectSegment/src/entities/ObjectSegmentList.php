<?php

namespace modules\objectSegment\src\entities;

use common\models\Employee;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\caching\TagDependency;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 *  * This is the model class for table "object_segment_list".
 *
 * @property int $osl_id
 * @property int $osl_ost_id
 * @property string $osl_key
 * @property string $osl_title
 * @property string $osl_description
 * @property bool $osl_enabled
 * @property bool $osl_is_system
 * @property int $osl_updated_user_id
 * @property string $osl_updated_dt
 * @property string $osl_created_dt
 * @property ObjectSegmentType $oslObjectSegmentType
 * @property Employee $oslUpdatedUser
 * @property ObjectSegmentTask[] $objectSegmentTaskAssigns
 *
 */
class ObjectSegmentList extends \yii\db\ActiveRecord
{
    public const CACHE_TAG = 'object-segment-list-tag-dependency';

    public static function tableName(): string
    {
        return 'object_segment_list';
    }

    public function rules(): array
    {
        return [
            [['osl_ost_id', 'osl_key', 'osl_title'], 'required'],
            [
                'osl_ost_id',
                'exist',
                'skipOnError'     => true,
                'targetClass'     => ObjectSegmentType::class,
                'targetAttribute' => ['osl_ost_id' => 'ost_id']
            ],
            [['osl_key', 'osl_ost_id'], 'unique', 'targetAttribute' => ['osl_key', 'osl_ost_id']],
            [['osl_key', 'osl_title'], 'string', 'max' => 100],
            [['osl_description'], 'string', 'max' => 1000],
            [['osl_enabled', 'osl_is_system'], 'boolean'],
        ];
    }

    public function getOslObjectSegmentType()
    {
        return $this->hasOne(ObjectSegmentType::class, ['ost_id' => 'osl_ost_id']);
    }

    public function getOslUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'osl_updated_user_id']);
    }

    public function getObjectSegmentTaskAssigns(): ActiveQuery
    {
        return $this->hasMany(ObjectSegmentTask::class, ['ostl_osl_id' => 'osl_id']);
    }


    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class'      => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['osl_created_dt', 'osl_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['osl_updated_dt'],
                ],
                'value'      => date('Y-m-d H:i:s')
            ],
            'attribute' => [
                'class'      => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['osl_updated_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['osl_updated_user_id'],
                ],
                'value'      => isset(Yii::$app->user) ? Yii::$app->user->id : null,
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        TagDependency::invalidate(Yii::$app->cache, self::CACHE_TAG);
    }

    public function attributeLabels(): array
    {
        return [
            'osl_id'              => 'ID',
            'osl_ost_id'          => 'Object Segment Type ID',
            'osl_key'             => 'Key',
            'osl_title'           => 'Title',
            'osl_description'     => 'Description',
            'osl_enabled'         => 'Enabled',
            'osl_updated_user_id' => 'Updated User ID',
            'osl_created_dt'      => 'Created Dt',
            'osl_updated_dt'      => 'Updated Dt',
            'osl_is_system' => 'Is System',
        ];
    }

    public static function getList(): array
    {
        $query = self::find()->select(['osl_id', 'osl_title']);

        return ArrayHelper::map(
            $query->all(),
            'osl_id',
            'osl_title'
        );
    }

    public static function getListCache(int $duration = 60 * 60): array
    {
        return Yii::$app->cache->getOrSet(self::CACHE_TAG, static function () {
            return self::getList();
        }, $duration, new TagDependency([
            'tags' => self::CACHE_TAG,
        ]));
    }
}
