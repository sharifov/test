<?php

namespace sales\model\kpi\entity\kpiUserPerformance;

use common\models\Employee;
use common\models\query\EmployeeQuery;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "kpi_user_performance".
 *
 * @property int $up_user_id
 * @property int $up_year
 * @property int $up_month
 * @property int|null $up_performance
 * @property int|null $up_created_user_id
 * @property int|null $up_updated_user_id
 * @property string|null $up_created_dt
 * @property string|null $up_updated_dt
 *
 * @property Employee $upCreatedUser
 * @property Employee $upUpdatedUser
 * @property Employee $upUser
 */
class KpiUserPerformance extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kpi_user_performance';
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
					ActiveRecord::EVENT_BEFORE_INSERT => ['up_created_dt', 'up_updated_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['up_updated_dt'],
				],
				'value' => date('Y-m-d H:i:s'),
				'preserveNonEmptyValues' => true
			],
			'user' => [
				'class' => BlameableBehavior::class,
				'createdByAttribute' => 'up_created_user_id',
				'updatedByAttribute' => 'up_updated_user_id',
			],
		];
	}

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['up_user_id', 'up_year', 'up_month'], 'required'],
            [['up_user_id', 'up_year', 'up_month', 'up_performance', 'up_created_user_id', 'up_updated_user_id'], 'integer'],
            [['up_created_dt', 'up_updated_dt'], 'safe'],
			[['up_month'], 'number', 'max' => 12, 'min' => 1],
			[['up_year'], 'string', 'max' => 4],
			[['up_year'], 'number', 'min' => 0],
			[['up_performance'], 'number', 'min' => 0, 'max' => 100],
            [['up_user_id', 'up_year', 'up_month'], 'unique', 'targetAttribute' => ['up_user_id', 'up_year', 'up_month']],
            [['up_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['up_created_user_id' => 'id']],
            [['up_updated_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['up_updated_user_id' => 'id']],
            [['up_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['up_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'up_user_id' => 'User ID',
            'up_year' => 'Year',
            'up_month' => 'Month',
            'up_performance' => 'Performance',
            'up_created_user_id' => 'Created ',
            'up_updated_user_id' => 'Updated ',
            'up_created_dt' => 'Created Dt',
            'up_updated_dt' => 'Updated Dt',
        ];
    }

    /**
     * Gets query for [[UpCreatedUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getUpCreatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'up_created_user_id']);
    }

    /**
     * Gets query for [[UpUpdatedUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getUpUpdatedUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'up_updated_user_id']);
    }

    /**
     * Gets query for [[UpUser]].
     *
     * @return \yii\db\ActiveQuery|EmployeeQuery
     */
    public function getUpUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'up_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return Scopes the active query used by this AR class.
     */
    public static function find()
    {
        return new Scopes(get_called_class());
    }
}
