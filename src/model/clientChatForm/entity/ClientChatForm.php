<?php

namespace src\model\clientChatForm\entity;

use common\components\validators\CheckJsonValidator;
use common\models\Employee;
use common\models\Project;
use src\behaviors\StringToJsonBehavior;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * @property int $ccf_id
 * @property string|null $ccf_key
 * @property string|null $ccf_name
 * @property int|null $ccf_project_id
 * @property string|null $ccf_dataform_json
 * @property int|null $ccf_enabled
 * @property int|null $ccf_created_user_id
 * @property int|null $ccf_updated_user_id
 * @property string|null $ccf_created_dt
 * @property string|null $ccf_updated_dt
 *
 * @property Employee $createdUser
 * @property Project $project
 * @property Employee $updatedUser
 */
class ClientChatForm extends ActiveRecord
{
    public const CACHE_DURATION = 60 * 60;

    public const KEY_BOOKING_ID = 'cc_form_booking_id';

    public function rules(): array
    {
        return [
            [['ccf_key', 'ccf_dataform_json'], 'required'],
            [['ccf_key', 'ccf_name', 'ccf_dataform_json'], 'trim'],

            ['ccf_dataform_json', CheckJsonValidator::class],

            ['ccf_enabled', 'integer'],

            ['ccf_key', 'string', 'max' => 100],
            ['ccf_key', 'unique'],

            ['ccf_name', 'string', 'max' => 100],

            ['ccf_project_id', 'integer'],
            ['ccf_project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['ccf_project_id' => 'id']],

            ['ccf_created_dt', 'safe'],

            ['ccf_created_user_id', 'integer'],
            ['ccf_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ccf_created_user_id' => 'id']],

            ['ccf_updated_dt', 'safe'],

            ['ccf_updated_user_id', 'integer'],
            ['ccf_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ccf_updated_user_id' => 'id']],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ccf_created_dt', 'ccf_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ccf_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ccf_created_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ccf_updated_user_id'],
                ]
            ],
            'stringToJson' => [
                'class' => StringToJsonBehavior::class,
                'jsonColumn' => 'ccf_dataform_json',
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ccf_created_user_id']);
    }

    public function getProject(): ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'ccf_project_id']);
    }

    public function getUpdatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ccf_updated_user_id']);
    }

    /**
     * @return array
     */
    public static function getList(): array
    {
        return self::find()->select(['ccf_name', 'ccf_id'])->orderBy(['ccf_name' => SORT_ASC])->indexBy('ccf_id')->cache(600)->asArray()->column();
    }

    public function attributeLabels(): array
    {
        return [
            'ccf_id' => 'ID',
            'ccf_key' => 'Key (unique)',
            'ccf_name' => 'Name',
            'ccf_project_id' => 'Project',
            'ccf_dataform_json' => 'Dataform Json',
            'ccf_enabled' => 'Enabled',
            'ccf_created_user_id' => 'Created User',
            'ccf_updated_user_id' => 'Updated User',
            'ccf_created_dt' => 'Created',
            'ccf_updated_dt' => 'Updated',
        ];
    }

    public static function find(): ClientChatFormScopes
    {
        return new ClientChatFormScopes(static::class);
    }

    public static function tableName(): string
    {
        return '{{%client_chat_form}}';
    }

    public static function getCacheKey(string $formKey, ?string $languageId): string
    {
        return 'client-chat_form-' . $formKey . '_' . $languageId;
    }
}
