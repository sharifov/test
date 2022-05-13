<?php

namespace common\models;

use common\models\query\GlobalLogQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "global_log".
 *
 * @property int $gl_id
 * @property string $gl_app_id
 * @property int $gl_app_user_id
 * @property string $gl_model
 * @property int $gl_obj_id
 * @property array $gl_old_attr
 * @property array $gl_new_attr
 * @property array $gl_formatted_attr
 * @property int $gl_action_type
 * @property string $gl_created_at
 *
 * @property Employee|null $userFrontend
 * @property string $glModel
 * @property string $appName
 * @property string $actionTypeName
 * @property string $modelName
 * @property ApiUser|null $userApi
 */
class GlobalLog extends ActiveRecord
{
    public const MODEL_CLIENT           = 'common\models\Client';
    public const MODEL_CLIENT_PHONE     = 'common\models\ClientPhone';
    public const MODEL_CLIENT_EMAIL     = 'common\models\ClientEmail';
    public const MODEL_CLIENT_LEAD      = 'common\models\Lead';
    public const MODEL_CLIENT_LEAD_PREFERENCES      = 'common\models\LeadPreferences';
    public const MODEL_LEAD_FLIGHT_SEGMENTS = 'common\models\LeadFlightSegment';
    public const MODEL_QUOTE    = 'common\models\Quote';
    public const MODEL_SETTING  = 'common\models\Setting';
    public const MODEL_CASES  = 'src\entities\cases\Cases';
    public const MODEL_LPPD  = 'src\model\leadPoorProcessingData\entity\LeadPoorProcessingData';
    public const MODEL_ABAC_POLICY  = 'modules\abac\src\entities\AbacPolicy';
    public const MODEL_EMPLOYEE         = 'common\models\Employee';
    public const MODEL_USER_PARAMS  = 'common\models\UserParams';
    public const MODEL_USER_PROFILE = 'common\models\UserProfile';
    public const MODEL_USER_PROJECT_PARAMS = 'common\models\UserProjectParams';
    public const MODEL_USER_VOICE_MAIL = 'src\model\userVoiceMail\useCase\manage\UserVoiceMailForm';
    public const MODEL_USER_PRODUCT_TYPE = 'common\models\UserProductType';

    public const MODEL_LIST         = [
        self::MODEL_CLIENT          => 'Client',
        self::MODEL_CLIENT_PHONE    => 'Client Phone',
        self::MODEL_CLIENT_EMAIL    => 'Client Email',
        self::MODEL_CLIENT_LEAD     => 'Lead',
        self::MODEL_CLIENT_LEAD_PREFERENCES => 'Lead Preferences',
        self::MODEL_LEAD_FLIGHT_SEGMENTS => 'LeadFlightSegment',
        self::MODEL_QUOTE => 'Quote',
        self::MODEL_SETTING => 'Setting',
        self::MODEL_CASES => 'Cases',
        self::MODEL_LPPD => 'LeadPoorProcessingData',
        self::MODEL_ABAC_POLICY => 'AbacPolicy',
        self::MODEL_EMPLOYEE        => 'Employee',
        self::MODEL_USER_PARAMS => 'UserParams',
        self::MODEL_USER_PROFILE => 'UserProfile',
        self::MODEL_USER_PROJECT_PARAMS => 'UserProjectParams',
        self::MODEL_USER_VOICE_MAIL => 'UserVoiceMailForm',
        self::MODEL_USER_PRODUCT_TYPE => 'UserProductType',
    ];

    public const APP_CONSOLE    = 'app-console';
    public const APP_FRONTEND   = 'app-frontend';
    public const APP_WEBAPI     = 'app-webapi';

    public const APP_LIST   = [
        self::APP_CONSOLE       => 'Console',
        self::APP_FRONTEND      => 'Frontend',
        self::APP_WEBAPI        => 'WebAPI',
    ];

    public const ACTION_TYPE_CREATE = 1;
    public const ACTION_TYPE_UPDATE = 2;

    public const ACTION_TYPE_LIST = [
        self::ACTION_TYPE_CREATE => 'Create',
        self::ACTION_TYPE_UPDATE => 'Update'
    ];

    public const ACTION_TYPE_AR = [
        ActiveRecord::EVENT_AFTER_INSERT => self::ACTION_TYPE_CREATE,
        ActiveRecord::EVENT_AFTER_UPDATE => self::ACTION_TYPE_UPDATE
    ];


    /**
     * @return string
     */
    public static function tableName()
    {
        return 'global_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gl_app_id', 'gl_model', 'gl_obj_id'], 'required'],
            [['gl_app_user_id', 'gl_obj_id', 'gl_action_type'], 'integer'],
            [['gl_old_attr', 'gl_new_attr', 'gl_formatted_attr', 'gl_created_at'], 'safe'],
            [['gl_app_id'], 'string', 'max' => 20],
            [['gl_model'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'gl_id' => 'ID',
            'gl_app_id' => 'Application',
            'gl_app_user_id' => 'App User',
            'gl_model' => 'Model',
            'gl_obj_id' => 'Object',
            'gl_old_attr' => 'Old Attr',
            'gl_new_attr' => 'New Attr',
            'gl_formatted_attr' => 'Formatted Attr',
            'gl_action_type' => 'Action',
            'gl_created_at' => 'DateTime',
            'glModel' => 'Model',
        ];
    }

    /**
     * {@inheritdoc}
     * @return GlobalLogQuery the active query used by this AR class.
     */
    public static function find(): GlobalLogQuery
    {
        return new GlobalLogQuery(static::class);
    }

    /**
     * @return array
     */
    public static function getAppList(): array
    {
        return self::APP_LIST;
    }

    /**
     * @return string
     */
    public function getAppName(): string
    {
        return self::APP_LIST[$this->gl_app_id] ?? '';
    }


    /**
     * @return array
     */
    public static function getModelList(): array
    {
        return self::MODEL_LIST;
    }

    /**
     * @return string
     */
    public function getModelName(): string
    {
        return self::MODEL_LIST[$this->gl_model] ?? '';
    }

    /**
     * @return array
     */
    public static function getActionTypeList(): array
    {
        return self::ACTION_TYPE_LIST;
    }

    /**
     * @return string
     */
    public function getActionTypeName(): string
    {
        return self::ACTION_TYPE_LIST[$this->gl_action_type] ?? '';
    }

    /**
     * @param string $actionTypeAR
     * @return int|null
     */
    public function getValueOfActionTypeByAR(string $actionTypeAR): ?int
    {
        return self::ACTION_TYPE_AR[$actionTypeAR] ?? null;
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    public function getGlModel()
    {
        return (new \ReflectionClass($this->gl_model))->getShortName();
    }

    /**
     * @return ActiveQuery|null
     */
    public function getUserFrontend(): ?ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'gl_app_user_id']);
    }

    /**
     * @return ActiveQuery|null
     */
    public function getUserApi(): ?ActiveQuery
    {
        return $this->hasOne(ApiUser::class, ['au_id' => 'gl_app_user_id']);
    }

    /**
     * @return bool
     */
    public function isAppFrontend(): bool
    {
        return $this->gl_app_id === self::APP_FRONTEND;
    }

    /**
     * @return bool
     */
    public function isAppWebApi(): bool
    {
        return $this->gl_app_id === self::APP_WEBAPI;
    }

    /**
     * @return bool
     */
    public function isAppConsole(): bool
    {
        return $this->gl_app_id === self::APP_CONSOLE;
    }
}
