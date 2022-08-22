<?php

namespace common\models;

use common\models\query\UserGroupQuery;
use src\dispatchers\NativeEventDispatcher;
use src\model\user\entity\userGroup\events\UserGroupEvents;
use Yii;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\caching\TagDependency;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "user_group".
 *
 * @property int $ug_id
 * @property string $ug_key
 * @property string $ug_name
 * @property string $ug_description
 * @property int $ug_disable
 * @property string $ug_updated_dt
 * @property int $ug_processing_fee
 * @property bool $ug_on_leaderboard
 * @property int $ug_user_group_set_id
 *
 * @property UserGroupAssign[] $userGroupAssigns
 * @property Employee[] $ugsUsers
 * @property UserGroupSet $userGroupSet
 */
class UserGroup extends ActiveRecord
{
    public const CACHE_KEY = 'user_group';
    public const CACHE_TAG_DEPENDENCY = 'user_group-tag-dependency';

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'user_group';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['ug_key', 'ug_name'], 'required'],
            [['ug_processing_fee', 'ug_disable'], 'integer'],
            [['ug_disable'], 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            [['ug_updated_dt'], 'safe'],
            [['ug_key', 'ug_name'], 'string', 'max' => 100],
            [['ug_description'], 'string', 'max' => 255],
            [['ug_key'], 'unique'],
            [['ug_on_leaderboard'], 'boolean'],
            ['ug_user_group_set_id', 'exist', 'targetClass' => UserGroupSet::class, 'targetAttribute' => ['ug_user_group_set_id' => 'ugs_id']]
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'ug_id' => 'ID',
            'ug_key' => 'Key',
            'ug_name' => 'Name',
            'ug_description' => 'Description',
            'ug_disable' => 'Disable',
            'ug_updated_dt' => 'Updated Dt',
            'ug_processing_fee' => 'Processing Fee',
            'ug_on_leaderboard' => 'Show on Leaderboard',
            'ug_user_group_set_id' => 'User Group Set',
        ];
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ug_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ug_updated_dt'],
//                    ActiveRecord::EVENT_AFTER_UPDATE => [[$object, 'sendWebHook']],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
        ];
    }

//    /**
//     * @return array
//     */
//    public function events(): array
//    {
//        return [
//            ActiveRecord::EVENT_AFTER_INSERT => [[$this, 'sendWebHook']],
//            ActiveRecord::EVENT_AFTER_UPDATE => [[$this, 'sendWebHook']],
//        ];
//    }

//    /**
//     * @param Event $event
//     */
//    public function sendWebHook(Event $event): void
//    {
//        Yii::warning('Changed 4Id: ' . $this->ug_id, 'User-Group: event');
//
//        if ($this->isAttributeChanged($this->ug_disable) || $this->isAttributeChanged($this->ug_name) || $this->isAttributeChanged($this->ug_key)) {
//            Yii::warning('Changed 5Id: ' . $this->ug_id, 'User-Group: event');
//        }
//    }

    /**
     * @return ActiveQuery
     */
    public function getUserGroupSet(): ActiveQuery
    {
        return $this->hasOne(UserGroupSet::class, ['ugs_id' => 'ug_user_group_set_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUserGroupAssigns(): ActiveQuery
    {
        return $this->hasMany(UserGroupAssign::class, ['ugs_group_id' => 'ug_id']);
    }

    /**
     * @return ActiveQuery
     * @throws InvalidConfigException
     */
    public function getUgsUsers(): ActiveQuery
    {
        return $this->hasMany(Employee::class, ['id' => 'ugs_user_id'])->viaTable('user_group_assign', ['ugs_group_id' => 'ug_id']);
    }

    /**
     * @return UserGroupQuery the active query used by this AR class.
     */
    public static function find(): UserGroupQuery
    {
        return new UserGroupQuery(static::class);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (self::CACHE_TAG_DEPENDENCY) {
            TagDependency::invalidate(Yii::$app->cache, self::CACHE_TAG_DEPENDENCY);
        }
        if ($insert) {
            NativeEventDispatcher::recordEvent(UserGroupEvents::class, UserGroupEvents::INSERT, [UserGroupEvents::class, 'webHookInsert'], $this->exportData());
            NativeEventDispatcher::trigger(UserGroupEvents::class, UserGroupEvents::INSERT);
        } else {
            if (isset($changedAttributes['ug_name']) || isset($changedAttributes['ug_key']) || isset($changedAttributes['ug_disable'])) {
                NativeEventDispatcher::recordEvent(
                    UserGroupEvents::class,
                    UserGroupEvents::UPDATE,
                    [UserGroupEvents::class, 'webHookUpdate'],
                    $this->exportData()
                );
                NativeEventDispatcher::trigger(UserGroupEvents::class, UserGroupEvents::UPDATE);
            }
        }
    }

    /**
     * @return bool
     */
    public function beforeDelete(): bool
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        NativeEventDispatcher::recordEvent(UserGroupEvents::class, UserGroupEvents::DELETE, [UserGroupEvents::class, 'webHookDelete'], $this->exportData());
        return true;
    }


    /**
     *
     */
    public function afterDelete(): void
    {
        parent::afterDelete();
        if (self::CACHE_TAG_DEPENDENCY) {
            TagDependency::invalidate(Yii::$app->cache, self::CACHE_TAG_DEPENDENCY);
        }
        NativeEventDispatcher::trigger(UserGroupEvents::class, UserGroupEvents::DELETE);
    }

    /**
     * @return array
     */
    public function exportData(): array
    {
        return [
            'ug_id' => $this->ug_id,
            'ug_key' => $this->ug_key,
            'ug_name' => $this->ug_name,
            'ug_disable' => $this->ug_disable,
            'ug_updated_dt' => $this->ug_updated_dt
        ];
    }

    /**
     * @return array
     */
    public static function getList(): array
    {
        $data = self::find()->orderBy(['ug_name' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data, 'ug_id', 'ug_name');
    }

    /**
     * @return array
     */
    public static function getEnabledList(): array
    {
        $data = self::find()->where('ug_disable=0')->orderBy(['ug_name' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data, 'ug_id', 'ug_name');
    }

    /**
     * @return array
     */
    public static function getEnvListWOCache(): array
    {
        $data = self::find()->orderBy(['ug_name' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data, 'ug_name', 'ug_name');
    }


    /**
     * @return array
     */
    public static function getEnvList(): array
    {
        if (self::CACHE_KEY) {
            $list = Yii::$app->cache->get(self::CACHE_KEY);
            if ($list === false) {
                $list = self::getEnvListWOCache();

                Yii::$app->cache->set(
                    self::CACHE_KEY,
                    $list,
                    0,
                    new TagDependency(['tags' => self::CACHE_TAG_DEPENDENCY])
                );
            }
        } else {
            $list = self::getEnvListWOCache();
        }

        return $list;
    }
}
