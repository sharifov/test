<?php

namespace sales\entities\log;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

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
 * @property string $gl_created_at
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
				'value' => date('Y-m-d H:i:s')
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
            [['gl_app_user_id', 'gl_obj_id'], 'integer'],
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
	 * @return static
	 */
    public static function create(
		string $glModel,
		int $glObjectId,
    	string $glAppId,
		?int $glAppUserId,
		?string $glOldAttr,
		?string $glNewAttr,
		?string $glFormattedAttr
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
		return $log;
	}

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'gl_id' => 'Gl ID',
            'gl_app_id' => 'Gl App ID',
            'gl_app_user_id' => 'Gl App User ID',
            'gl_model' => 'Gl Model',
            'gl_obj_id' => 'Gl Obj ID',
            'gl_old_attr' => 'Gl Old Attr',
            'gl_new_attr' => 'Gl New Attr',
            'gl_formatted_attr' => 'Gl Formatted Attr',
            'gl_created_at' => 'Gl Created At',
        ];
    }
}
