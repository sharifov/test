<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "employees_activity".
 *
 * @property int $id
 * @property int $employee_id
 * @property string $user_ip
 * @property string $created
 * @property string $request
 * @property string $request_type
 * @property string $request_params
 * @property string $request_header
 *
 * @property Employee $employee
 */
class EmployeeActivity extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employees_activity';
    }

    /**
     * @param $employeeId
     * @param int $lastLimit
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getEmployeeActivity($employeeId, $lastLimit = 50)
    {
        $activities = self::find()
            ->where(['employee_id' => $employeeId])
            ->limit($lastLimit)->orderBy('id DESC')
            ->all();

        return $activities;
    }

    /**
     * @param $employeeId
     * @return string
     */
    public static function getEmployeeLastActivity($employeeId)
    {
        /**
         * @var $activity self
         */
        $activity = self::find()
            ->where(['employee_id' => $employeeId])
            ->limit(1)->orderBy('id DESC')
            ->one();
        if ($activity !== null) {
            $now = new \DateTime();
            $created = new \DateTime($activity->created);
            $interval = $now->diff($created);
            $return = [];
            if ($interval->format('%y') > 0) {
                $return[] = $interval->format('%y') . 'y';
            }
            if ($interval->format('%m') > 0) {
                $return[] = $interval->format('%m') . 'mh';
            }
            if ($interval->format('%d') > 0) {
                $return[] = $interval->format('%d') . 'd';
            }
            if ($interval->format('%i') >= 0 && $interval->format('%h') >= 0) {
                $return[] = $interval->format('%h') . 'h ' . $interval->format('%I') . 'm';
            }

            return implode(' ', $return);
        }
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['employee_id'], 'integer'],
            [['created'], 'safe'],
            [['request_params', 'request_header'], 'string'],
            [['user_ip', 'request', 'request_type'], 'string', 'max' => 255],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['employee_id' => 'id']],
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
            'user_ip' => 'User Ip',
            'created' => 'Created',
            'request' => 'Request',
            'request_type' => 'Request Type',
            'request_params' => 'Request Params',
            'request_header' => 'Request Header',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::className(), ['id' => 'employee_id']);
    }

    public function beforeValidate()
    {
        if (!empty($this->request_params)) {
            $this->request_params = urlencode(serialize($this->request_params));
        }
        if (!empty($this->request_header)) {
            $this->request_header = urlencode(serialize($this->request_header));
        }

        return parent::beforeValidate();
    }

    public function afterFind()
    {
        parent::afterFind();
        if (!empty($this->request_params)) {
            $this->request_params = unserialize(urldecode($this->request_params));
        }
        if (!empty($this->request_header)) {
            $this->request_header = unserialize(urldecode($this->request_header));
        }
    }
}
