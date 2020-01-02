<?php

namespace common\models;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\query\UserProjectParamsQuery;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_project_params".
 *
 * @property int $upp_user_id
 * @property int $upp_project_id
 * @property string $upp_email
 * @property string $upp_phone_number
 * @property string $upp_tw_phone_number
 * @property string $upp_created_dt
 * @property string $upp_updated_dt
 * @property int $upp_updated_user_id
 * @property bool $upp_allow_general_line
 * @property int $upp_dep_id
 *
 * @property Project $uppProject
 * @property Employee $uppUpdatedUser
 * @property Employee $uppUser
 * @property Department $uppDep
 */
class UserProjectParams extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_project_params';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['upp_user_id', 'upp_project_id'], 'required'],
            [['upp_user_id', 'upp_project_id', 'upp_updated_user_id', 'upp_dep_id'], 'integer'],
            [['upp_created_dt', 'upp_updated_dt'], 'safe'],
            [['upp_email'], 'string', 'max' => 100],
            [['upp_email'], 'trim'],
            [['upp_email'], 'email'],

            ['upp_tw_phone_number', 'unique', 'targetAttribute' => ['upp_tw_phone_number']], //, 'message' => 'Twillio Phone Number must be unique'],

            [['upp_phone_number', 'upp_tw_phone_number'], 'string', 'max' => 30],
            [['upp_user_id', 'upp_project_id'], 'unique', 'targetAttribute' => ['upp_user_id', 'upp_project_id']],
            [['upp_project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['upp_project_id' => 'id']],
            [['upp_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['upp_updated_user_id' => 'id']],
            [['upp_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['upp_user_id' => 'id']],
            [['upp_dep_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['upp_dep_id' => 'dep_id']],
            [['upp_phone_number', 'upp_tw_phone_number'], PhoneInputValidator::class],

            ['upp_allow_general_line', 'boolean']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'upp_user_id' => 'User',
            'upp_project_id' => 'Project',
            'upp_email' => 'Email',
            'upp_phone_number' => 'Old Phone Number',
            'upp_tw_phone_number' => 'Phone Number',
            'upp_created_dt' => 'Created Dt',
            'upp_updated_dt' => 'Updated Dt',
            'upp_updated_user_id' => 'Updated User',
            'upp_allow_general_line' => 'Allow General Line',
            'upp_dep_id' => 'Department'
        ];
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['upp_created_dt', 'upp_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['upp_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
            ],
            'attribute' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['upp_updated_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['upp_updated_user_id'],
                ],
                'value' => isset(Yii::$app->user) ? Yii::$app->user->id : null,
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUppDep()
    {
        return $this->hasOne(Department::class, ['dep_id' => 'upp_dep_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUppProject()
    {
        return $this->hasOne(Project::class, ['id' => 'upp_project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUppUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'upp_updated_user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUppUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'upp_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return UserProjectParamsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserProjectParamsQuery(static::class);
    }
}
