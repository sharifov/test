<?php

namespace sales\model\clientChatChannel\entity;

use common\models\Department;
use common\models\Employee;
use common\models\Project;
use common\models\UserGroup;
use sales\model\clientChat\entity\channelTranslate\ClientChatChannelTranslate;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\caching\TagDependency;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "client_chat_channel".
 *
 * @property int $ccc_id
 * @property string $ccc_name
 * @property int|null $ccc_project_id
 * @property int|null $ccc_dep_id
 * @property int|null $ccc_ug_id
 * @property int|null $ccc_disabled
 * @property int|null $ccc_priority
 * @property string|null $ccc_created_dt
 * @property string|null $ccc_updated_dt
 * @property int|null $ccc_created_user_id
 * @property int|null $ccc_updated_user_id
 * @property int|null $ccc_default
 * @property string $ccc_frontend_name
 * @property int|null $ccc_frontend_enabled
 * @property string|null $ccc_settings
 * @property array $settings
 * @property bool $ccc_registered
 * @property bool $ccc_default_device
 *
 * @property Employee $cccCreatedUser
 * @property Department $cccDep
 * @property Project $cccProject
 * @property UserGroup $cccUg
 * @property Employee $cccUpdatedUser
 * @property ClientChatChannelTranslate[] $clientChatChannelTranslates
 * @property ClientChat[] $cch
 */
class ClientChatChannel extends \yii\db\ActiveRecord
{
    public const MAX_PRIORITY_VALUE = 100;

    private array $decodedSettings = [];

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ccc_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ccc_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ccc_created_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ccc_updated_user_id'],
                ]
            ],
        ];
    }

    public function rules(): array
    {
        return [
            ['ccc_created_dt', 'safe'],

            ['ccc_created_user_id', 'integer'],
            ['ccc_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ccc_created_user_id' => 'id']],

            ['ccc_default', 'integer'],
            [['ccc_frontend_enabled', 'ccc_default_device'], 'boolean'],

            ['ccc_default_device', 'validateDefaultDevice'],

            ['ccc_dep_id', 'integer'],
            ['ccc_dep_id', 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['ccc_dep_id' => 'dep_id']],

            ['ccc_disabled', 'integer'],
            ['ccc_priority', 'integer', 'max' => self::MAX_PRIORITY_VALUE, 'min' => 1],
            ['ccc_priority', 'default', 'value' => 1],

            [['ccc_name', 'ccc_frontend_name'], 'required'],
            ['ccc_name', 'filter', 'filter' => 'trim'],
            ['ccc_name', 'string', 'max' => 255],
            ['ccc_name', 'unique'],
            [['ccc_frontend_name'], 'string', 'max' => 100],
            ['ccc_frontend_name', 'filter', 'filter' => 'trim'],

            ['ccc_project_id', 'required'],
            ['ccc_project_id', 'integer'],
            ['ccc_project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['ccc_project_id' => 'id']],
//            [['ccc_project_id', 'ccc_dep_id'], 'unique', 'targetAttribute' => ['ccc_project_id', 'ccc_dep_id']],

            ['ccc_ug_id', 'integer'],
            ['ccc_ug_id', 'exist', 'skipOnError' => true, 'targetClass' => UserGroup::class, 'targetAttribute' => ['ccc_ug_id' => 'ug_id']],

            [['ccc_updated_dt'], 'safe'],

            [['ccc_settings'], 'string'],


            ['ccc_updated_user_id', 'integer'],
            ['ccc_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ccc_updated_user_id' => 'id']],

            ['ccc_registered', 'boolean'],
        ];
    }

    public function beforeSave($insert): bool
    {
        $defaultChannel = self::findOne(['ccc_default' => 1, 'ccc_project_id' => $this->ccc_project_id]);
        if (!$defaultChannel && $insert) {
            $this->ccc_default = 1;
        }
        return parent::beforeSave($insert);
    }

    public function afterDelete(): void
    {
        $defaultChannel = self::findOne(['ccc_default' => 1, 'ccc_project_id' => $this->ccc_project_id, ['<>', 'ccc_id', $this->ccc_id]]);
        if (!$defaultChannel && $firstChannel = self::findOne(['ccc_project_id' => $this->ccc_project_id])) {
            $firstChannel->ccc_default = 1;
            $firstChannel->save();
        }
    }

    public function getCccCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ccc_created_user_id']);
    }

    public function getCccDep(): ActiveQuery
    {
        return $this->hasOne(Department::class, ['dep_id' => 'ccc_dep_id']);
    }

    public function getCccProject(): ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'ccc_project_id']);
    }

    public function getCccUg(): ActiveQuery
    {
        return $this->hasOne(UserGroup::class, ['ug_id' => 'ccc_ug_id']);
    }

    public function getCccUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ccc_updated_user_id']);
    }

    /**
     * Gets query for [[ClientChatChannelTranslates]].
     *
     * @return ActiveQuery
     */
    public function getClientChatChannelTranslates(): ActiveQuery
    {
        return $this->hasMany(ClientChatChannelTranslate::class, ['ct_channel_id' => 'ccc_id']);
    }

    public function getCch(): ActiveQuery
    {
        return $this->hasMany(ClientChat::class, ['cch_channel_id' => 'ccc_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ccc_id' => 'ID',
            'ccc_name' => 'Name',
            'ccc_project_id' => 'Project',
            'ccc_dep_id' => 'Department',
            'ccc_ug_id' => 'User Group',
            'ccc_disabled' => 'Disabled',
            'ccc_priority' => 'Priority',
            'ccc_created_dt' => 'Created Dt',
            'ccc_updated_dt' => 'Updated Dt',
            'ccc_created_user_id' => 'Created User ID',
            'ccc_updated_user_id' => 'Updated User ID',
            'ccc_default' => 'Default',
            'ccc_frontend_name' => 'Frontend Name',
            'ccc_frontend_enabled' => 'Frontend Enabled',
            'ccc_settings' => 'Settings',
            'ccc_registered' => 'Registered',
            'ccc_default_device' => 'Default Device'
        ];
    }

    public function getSettings(): array
    {
        if (!empty($this->decodedSettings)) {
            return $this->decodedSettings;
        }
        return $this->decodedSettings = ArrayHelper::merge(ClientChatChannelDefaultSettings::getAll(), Json::decode($this->ccc_settings) ?? []);
    }

    /**
     * @return Scopes
     */
    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'client_chat_channel';
    }

    /**
     * @return array
     */
    public static function getList(): array
    {
        $data = self::find()->orderBy(['ccc_name' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data, 'ccc_id', 'ccc_name');
    }

    public static function getListWithNames(): array
    {
        $data = self::find()->where('LENGTH(ccc_name) > 0')->distinct()->orderBy(['ccc_name' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data, 'ccc_name', 'ccc_name');
    }

    public static function getListWithFrontedNames(): array
    {
        $data = self::find()->where('LENGTH(ccc_frontend_name) > 0')->distinct()->orderBy(['ccc_frontend_name' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($data, 'ccc_frontend_name', 'ccc_frontend_name');
    }

    public function isAllowedTransferToChannel(): bool
    {
        return (bool)($this->settings['system']['allowTransferChannelActiveChat'] ?? ClientChatChannelDefaultSettings::isAllowTransferChannelActiveChat());
    }

    /**
     * @param int $projectId
     * @param string|null $languageId
     * @return array
     */
    public static function getSettingsList(int $projectId, ?string $languageId = null): array
    {
        $dataList = [];
        $channelList = self::find()
            ->where(['ccc_disabled' => false, 'ccc_frontend_enabled' => true, 'ccc_project_id' => $projectId])
            ->orWhere(['ccc_default' => true, 'ccc_project_id' => $projectId])
            ->orderBy(['ccc_priority' => SORT_ASC])->all();
        if ($channelList) {
            foreach ($channelList as $channelItem) {
                if ($channelItem->ccc_settings) {
                    $settings = @json_decode($channelItem->ccc_settings, true);
                    $settings = $settings ?: [];
                } else {
                    $settings = [];
                }

                $translateName = null;
                if ($languageId) {
                    $translateName = ClientChatChannelTranslate::find()->select(['ct_name'])->where(['ct_channel_id' => $channelItem->ccc_id, 'ct_language_id' => $languageId])->limit(1)->scalar();
                }

                $dataList[] = [
                    'id' => $channelItem->ccc_id,
                    'name' => $translateName ?: $channelItem->ccc_frontend_name,
                    'priority' => $channelItem->ccc_priority,
                    'default' => (bool) $channelItem->ccc_default,
                    'enabled' => (bool) $channelItem->ccc_frontend_enabled,
                    'defaultDevice' => (bool) $channelItem->ccc_default_device,
                    'settings' => $settings
                ];
            }
        }
        return $dataList;
    }

    public function registered(): void
    {
        $this->ccc_registered = true;
    }

    public function unRegistered(): void
    {
        $this->ccc_registered = false;
    }

    public static function getListByUserId(?int $userId): ?array
    {
        if (!$userId) {
            return null;
        }

        return Yii::$app->cache->getOrSet(
            ClientChatUserChannel::userCacheName($userId),
            static function () use ($userId) {
                return ClientChatChannel::find()
                    ->select(['ccc_name', 'ccc_id'])
                    ->joinWithCcuc($userId)
                    ->indexBy('ccc_id')
                    ->column();
            },
            ClientChatUserChannel::CACHE_DURATION,
            new TagDependency([
                'tags' => ClientChatUserChannel::cacheTags($userId),
            ])
        );
    }

    public static function getPubSubKey(?int $channelId): string
    {
        return 'channel-' . (int) $channelId;
    }

    public function validateDefaultDevice($attribute, $params)
    {
        if (self::find()->where(['ccc_project_id' => $this->ccc_project_id])->andWhere(['ccc_default_device' => true])->exists() && $this->ccc_default_device && $this->isNewRecord) {
            $this->addError($attribute, 'Default Device already set for project ' . ($this->cccProject ? $this->cccProject->name : ''));
        }
    }
}
