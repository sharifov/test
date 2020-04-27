<?php

namespace common\models;

use Yii;

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
            'cp_client_id' => 'Cp Client ID',
            'cp_project_id' => 'Cp Project ID',
            'cp_created_dt' => 'Cp Created Dt',
        ];
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
