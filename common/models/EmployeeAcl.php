<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "employee_acl".
 *
 * @property int $id
 * @property int $employee_id
 * @property string $mask
 * @property int $active
 * @property string $created
 * @property string $updated
 * @property string $description
 *
 * @property Employee $employee
 */
class EmployeeAcl extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employee_acl';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['employee_id', 'active'], 'integer'],
            [['mask', 'description'], 'required'],
            [['created', 'updated'], 'safe'],
            ['mask', 'ip', 'ipv6' => false],
            ['mask', 'unique', 'targetAttribute' => ['mask', 'employee_id']],
            [['description'], 'string', 'max' => 255],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['employee_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'employee_id' => 'Employee ID',
            'mask' => 'IP',
            'active' => 'Active',
            'created' => 'Created',
            'updated' => 'Updated',
            'description' => 'Description'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['id' => 'employee_id']);
    }

    public function afterValidate()
    {
        parent::afterValidate();
        $this->updated = date('Y-m-d H:i:s');
    }

    public static function isActiveIPRule($ip)
    {
        $id = Yii::$app->user->id ? Yii::$app->user->id : 0;

        return self::findOne([
            'active' => true,
            'mask' => trim($ip),
            'employee_id' => $id
        ]);
    }
}
