<?php

namespace sales\model\clientChatChannel\entity;

use common\models\Department;
use common\models\Employee;
use common\models\Project;
use common\models\UserGroup;
use sales\model\clientChat\entity\ClientChat;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

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
 *
 * @property Employee $cccCreatedUser
 * @property Department $cccDep
 * @property Project $cccProject
 * @property UserGroup $cccUg
 * @property Employee $cccUpdatedUser
 * @property ClientChat[] $cch
 */
class ClientChatChannel extends \yii\db\ActiveRecord
{
	public const MAX_PRIORITY_VALUE = 100;

	public function behaviors(): array
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['ccc_created_dt', 'ccc_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['ccc_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s'),
			],
			'user' => [
				'class' => BlameableBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['ccc_created_user_id', 'ccc_updated_user_id'],
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

            ['ccc_dep_id', 'integer'],
            ['ccc_dep_id', 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['ccc_dep_id' => 'dep_id']],

            ['ccc_disabled', 'integer'],
            ['ccc_priority', 'integer', 'max' => self::MAX_PRIORITY_VALUE, 'min' => 1],
            ['ccc_priority', 'default', 'value' => 1],

            ['ccc_name', 'required'],
            ['ccc_name', 'string', 'max' => 255],
            ['ccc_name', 'unique'],

            ['ccc_project_id', 'integer'],
            ['ccc_project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['ccc_project_id' => 'id']],

            ['ccc_ug_id', 'integer'],
            ['ccc_ug_id', 'exist', 'skipOnError' => true, 'targetClass' => UserGroup::class, 'targetAttribute' => ['ccc_ug_id' => 'ug_id']],

            ['ccc_updated_dt', 'safe'],

            ['ccc_updated_user_id', 'integer'],
            ['ccc_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ccc_updated_user_id' => 'id']],
        ];
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
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'client_chat_channel';
    }
}
