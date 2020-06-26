<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "email_unsubscribe".
 *
 * @property string $eu_email
 * @property int $eu_project_id
 * @property int|null $eu_created_user_id
 * @property string|null $eu_created_dt
 */
class EmailUnsubscribe extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'email_unsubscribe';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['eu_email', 'eu_project_id'], 'required'],
            [['eu_project_id', 'eu_created_user_id'], 'integer'],
            [['eu_created_dt'], 'safe'],
            [['eu_email'], 'string', 'max' => 160],
            [['eu_email', 'eu_project_id'], 'unique', 'targetAttribute' => ['eu_email', 'eu_project_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'eu_email' => 'Email',
            'eu_project_id' => 'Project ID',
            'eu_created_user_id' => 'Created User ID',
            'eu_created_dt' => 'Created Dt',
        ];
    }
}
