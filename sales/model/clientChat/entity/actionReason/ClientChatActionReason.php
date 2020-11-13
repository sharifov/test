<?php

namespace sales\model\clientChat\entity\actionReason;

use common\models\Employee;
use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "client_chat_action_reason".
 *
 * @property int $ccar_id
 * @property int $ccar_action_id
 * @property string|null $ccar_key
 * @property string|null $ccar_name
 * @property int|null $ccar_enabled
 * @property int|null $ccar_comment_required
 * @property int|null $ccar_created_user_id
 * @property int|null $ccar_updated_user_id
 * @property string|null $ccar_created_dt
 * @property string|null $ccar_updated_dt
 *
 * @property Employee $ccarCreatedUser
 * @property Employee $ccarUpdatedUser
 */
class ClientChatActionReason extends \yii\db\ActiveRecord
{
	public function behaviors(): array
	{
		$behaviors = [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['ccar_created_dt', 'ccar_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['ccar_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s')
			],
			'user' => [
				'class' => BlameableBehavior::class,
				'createdByAttribute' => 'ccar_created_user_id',
				'updatedByAttribute' => 'ccar_updated_user_id',
			],
		];
		return ArrayHelper::merge(parent::behaviors(), $behaviors);
	}

    public function rules(): array
    {
        return [
            ['ccar_action_id', 'required'],
            ['ccar_action_id', 'integer'],
			['ccar_action_id', 'in', 'range' => array_keys(ClientChatStatusLog::getActionList())],

            ['ccar_comment_required', 'integer'],

            ['ccar_created_dt', 'safe'],

            ['ccar_created_user_id', 'integer'],
            ['ccar_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ccar_created_user_id' => 'id']],

            ['ccar_enabled', 'integer'],

            ['ccar_key', 'string', 'max' => 50],

            ['ccar_name', 'string', 'max' => 50],

            ['ccar_updated_dt', 'safe'],

            ['ccar_updated_user_id', 'integer'],
            ['ccar_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['ccar_updated_user_id' => 'id']],
        ];
    }

    public function getCcarCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ccar_created_user_id']);
    }

    public function getCcarUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'ccar_updated_user_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'ccar_id' => 'ID',
            'ccar_action_id' => 'Action ID',
            'ccar_key' => 'Key',
            'ccar_name' => 'Name',
            'ccar_enabled' => 'Enabled',
            'ccar_comment_required' => 'Comment Required',
            'ccar_created_user_id' => 'Created User ID',
            'ccar_updated_user_id' => 'Updated User ID',
            'ccar_created_dt' => 'Created Dt',
            'ccar_updated_dt' => 'Updated Dt',
        ];
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }

    public static function tableName(): string
    {
        return 'client_chat_action_reason';
    }
}
