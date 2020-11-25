<?php

namespace sales\model\clientChat\entity\projectConfig;

use common\models\Employee;
use common\models\Project;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "client_chat_project_config".
 *
 * @property int $ccpc_project_id
 * @property string|null $ccpc_params_json
 * @property string|null $ccpc_theme_json
 * @property string|null $ccpc_registration_json
 * @property string|null $ccpc_settings_json
 * @property int|null $ccpc_enabled
 * @property int|null $ccpc_created_user_id
 * @property int|null $ccpc_updated_user_id
 * @property string|null $ccpc_created_dt
 * @property string|null $ccpc_updated_dt
 *
 * @property Employee $ccpcCreatedUser
 * @property Project $ccpcProject
 * @property Employee $ccpcUpdatedUser
 */
class ClientChatProjectConfig extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_chat_project_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ccpc_project_id'], 'required'],
            [['ccpc_project_id', 'ccpc_enabled', 'ccpc_created_user_id', 'ccpc_updated_user_id'], 'integer'],
            [['ccpc_params_json', 'ccpc_theme_json', 'ccpc_registration_json', 'ccpc_settings_json', 'ccpc_created_dt', 'ccpc_updated_dt'], 'safe'],
            [['ccpc_project_id'], 'unique'],
            [['ccpc_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ccpc_created_user_id' => 'id']],
            [['ccpc_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['ccpc_project_id' => 'id']],
            [['ccpc_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ccpc_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ccpc_project_id' => 'Project ID',
            'ccpc_params_json' => 'Params Json',
            'ccpc_theme_json' => 'Theme Json',
            'ccpc_registration_json' => 'Registration Json',
            'ccpc_settings_json' => 'Settings Json',
            'ccpc_enabled' => 'Enabled',
            'ccpc_created_user_id' => 'Created User ID',
            'ccpc_updated_user_id' => 'Updated User ID',
            'ccpc_created_dt' => 'Created Dt',
            'ccpc_updated_dt' => 'Updated Dt',
        ];
    }

    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['ccpc_created_dt', 'ccpc_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['ccpc_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'ccpc_created_user_id',
                'updatedByAttribute' => 'ccpc_updated_user_id',
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * Gets query for [[CcpcCreatedUser]].
     *
     * @return ActiveQuery
     */
    public function getCcpcCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'ccpc_created_user_id']);
    }

    /**
     * Gets query for [[CcpcProject]].
     *
     * @return ActiveQuery
     */
    public function getCcpcProject()
    {
        return $this->hasOne(Project::class, ['id' => 'ccpc_project_id']);
    }

    /**
     * Gets query for [[CcpcUpdatedUser]].
     *
     * @return ActiveQuery
     */
    public function getCcpcUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'ccpc_updated_user_id']);
    }

    /**
     * @param int $projectId
     * @param string|null $languageId
     * @return string
     */
    public static function getCacheKey(int $projectId, ?string $languageId): string
    {
        return 'chat-config-' . $projectId . '-' . $languageId;
    }
}
