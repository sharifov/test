<?php

namespace sales\entities\log;

use common\models\ApiUser;
use common\models\Client;
use common\models\ClientEmail;
use common\models\ClientPhone;
use common\models\Employee;
use common\models\Lead;
use common\models\LeadPreferences;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * This is the model class for table "global_logs".
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
 * @property Employee|ApiUser|null $user
 */
class GlobalLog extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'global_log';
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
					ActiveRecord::EVENT_BEFORE_INSERT => ['gl_created_at']
				],
				'value' => static function ($model) {
					if ($model->sender->gl_created_at) {
						return $model->sender->gl_created_at;
					}
					return date('Y-m-d H:i:s');
				}
			],
		];
	}

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['gl_app_id', 'gl_model', 'gl_obj_id'], 'required'],
            [['gl_app_user_id', 'gl_obj_id', 'gl_action_type'], 'integer'],
            [['gl_old_attr', 'gl_new_attr', 'gl_formatted_attr', 'gl_created_at'], 'safe'],
            [['gl_app_id'], 'string', 'max' => 20],
            [['gl_model'], 'string', 'max' => 50],
        ];
    }

	/**
	 * @param string $glModel
	 * @param int $glObjectId
	 * @param string $glAppId
	 * @param int|null $glAppUserId
	 * @param string|null $glOldAttr
	 * @param string|null $glNewAttr
	 * @param string|null $glFormattedAttr
	 * @param int|null $glActionType
	 * @param string|null $glCreatedAt
	 * @return static
	 */
    public static function create(
		string $glModel,
		int $glObjectId,
    	string $glAppId,
		?int $glAppUserId,
		?string $glOldAttr,
		?string $glNewAttr,
		?string $glFormattedAttr,
		?int $glActionType,
		?string $glCreatedAt
	): self
	{
		$log = new static();
		$log->gl_app_id = $glAppId;
		$log->gl_app_user_id = $glAppUserId;
		$log->gl_model = $glModel;
		$log->gl_obj_id = $glObjectId;
		$log->gl_old_attr = $glOldAttr;
		$log->gl_new_attr = $glNewAttr;
		$log->gl_formatted_attr = $glFormattedAttr;
		$log->gl_action_type = $glActionType;
		$log->gl_created_at = $glCreatedAt;
		return $log;
	}

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'gl_id' => 'Id',
            'gl_app_id' => 'Application',
            'gl_app_user_id' => 'Who made the changes',
            'gl_model' => 'Model',
            'gl_obj_id' => 'Object id',
            'gl_old_attr' => 'Old attributes',
            'gl_new_attr' => 'New attributes',
            'gl_formatted_attr' => 'Formatted Attributes',
            'gl_created_at' => 'When changes were made',
			'gl_action_type' => 'Action',
			'glModel' => 'Model'
        ];
    }
}
