<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "department_email_project_user_group".
 *
 * @property int $dug_dep_id
 * @property int $dug_ug_id
 * @property string $dug_created_dt
 *
 * @property DepartmentEmailProject $dugDep
 * @property UserGroup $dugUg
 */
class DepartmentEmailProjectUserGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'department_email_project_user_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['dug_dep_id', 'dug_ug_id'], 'required'],
            [['dug_dep_id', 'dug_ug_id'], 'integer'],
            [['dug_created_dt'], 'safe'],
            [['dug_dep_id', 'dug_ug_id'], 'unique', 'targetAttribute' => ['dug_dep_id', 'dug_ug_id']],
            [['dug_dep_id'], 'exist', 'skipOnError' => true, 'targetClass' => DepartmentEmailProject::class, 'targetAttribute' => ['dug_dep_id' => 'dep_id']],
            [['dug_ug_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserGroup::class, 'targetAttribute' => ['dug_ug_id' => 'ug_id']],
        ];
    }

	/**
	 * @return array
	 */
	public function behaviors()
	{
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
				'attributes' => [
					ActiveRecord::EVENT_BEFORE_INSERT => ['dug_created_dt'],
					ActiveRecord::EVENT_BEFORE_UPDATE => ['dug_created_dt'],
				],
				'value' => date('Y-m-d H:i:s') //new Expression('NOW()'),
			],
		];
	}

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dug_dep_id' => 'Dug Dep ID',
            'dug_ug_id' => 'Dug Ug ID',
            'dug_created_dt' => 'Dug Created Dt',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDugDep()
    {
        return $this->hasOne(DepartmentEmailProject::class, ['dep_id' => 'dug_dep_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDugUg()
    {
        return $this->hasOne(UserGroup::class, ['ug_id' => 'dug_ug_id']);
    }
}
