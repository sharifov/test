<?php

namespace modules\shiftSchedule\src\entities\shiftCategory;

use common\models\Employee;
use common\models\query\EmployeeQuery;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "shift_category".
 *
 * @property int $sc_id
 * @property string $sc_name
 * @property int|null $sc_created_user_id
 * @property int|null $sc_updated_user_id
 * @property string|null $sc_created_dt
 * @property string|null $sc_updated_dt
 *
 * @property Employee $createdUser
 * @property Employee $updatedUser
 */
class ShiftCategory extends \yii\db\ActiveRecord
{
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['sc_created_dt', 'sc_updated_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['sc_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s'),
            ],
            'user' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['sc_created_user_id', 'sc_updated_user_id'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['sc_updated_user_id'],
                ]
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'shift_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sc_name'], 'required'],
            [['sc_created_user_id', 'sc_updated_user_id'], 'integer'],
            [['sc_created_dt', 'sc_updated_dt'], 'safe'],
            [['sc_name'], 'string', 'max' => 50],
            [['sc_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['sc_created_user_id' => 'id']],
            [['sc_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['sc_updated_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'sc_id' => 'ID',
            'sc_name' => 'Name',
            'sc_created_user_id' => 'Created User ID',
            'sc_updated_user_id' => 'Updated User ID',
            'sc_created_dt' => 'Created Dt',
            'sc_updated_dt' => 'Updated Dt',
        ];
    }

    /**
     * Gets query for [[ScCreatedUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'sc_created_user_id']);
    }

    /**
     * Gets query for [[ScUpdatedUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'sc_updated_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return ShiftCategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ShiftCategoryQuery(get_called_class());
    }
}
