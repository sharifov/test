<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

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

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['eu_created_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
        ];
        return ArrayHelper::merge(parent::behaviors(), $behaviors);
    }

    /**
     * @param string $email
     * @param int $projectId
     * @return static
     */
    public static function create(string $email, int $projectId): self
    {
        $model = new static();
        $model->eu_email = $email;
        $model->eu_project_id = $projectId;
        return $model;
    }

    public static function emailInList(string $email, ?int $projectId = null): bool
    {
        $query = self::find()
            ->where([
                'eu_email' => $email,
            ])
            ->limit(1);

        if ($projectId !== null) {
            $query->andWhere([
                'eu_project_id' => $projectId,
            ]);
        }

        return $query->exists();
    }
}
