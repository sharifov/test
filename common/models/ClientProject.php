<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "client_project".
 *
 * @property int $cp_client_id
 * @property int $cp_project_id
 * @property string|null $cp_created_dt
 *
 * @property Client $cpClient
 * @property Project $cpProject
 */
class ClientProject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client_project';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cp_client_id', 'cp_project_id'], 'required'],
            [['cp_client_id', 'cp_project_id'], 'integer'],
            [['cp_created_dt'], 'safe'],
            [['cp_client_id', 'cp_project_id'], 'unique', 'targetAttribute' => ['cp_client_id', 'cp_project_id']],
            [['cp_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['cp_project_id' => 'id']],
            [['cp_client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Client::class, 'targetAttribute' => ['cp_client_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cp_client_id' => 'Client ID',
            'cp_project_id' => 'Project',
            'cp_created_dt' => 'Created',
        ];
    }

    public function behaviors(): array
	{
		$behaviors = [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['cp_created_dt'],
				],
				'value' => date('Y-m-d H:i:s')
			],
		];
		return ArrayHelper::merge(parent::behaviors(), $behaviors);
	}

    /**
     * Gets query for [[CpClient]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCpClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'cp_client_id']);
    }

    /**
     * Gets query for [[CpProject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCpProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'cp_project_id']);
    }
}
