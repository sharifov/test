<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "lead_task".
 *
 * @property int $lt_lead_id
 * @property int $lt_task_id
 * @property int $lt_user_id
 * @property string $lt_date
 * @property string $lt_notes
 * @property string $lt_completed_dt
 * @property string $lt_updated_dt
 *
 * @property Lead $ltLead
 * @property Task $ltTask
 * @property Employee $ltUser
 */
class LeadTask extends \yii\db\ActiveRecord
{
    public $field_cnt;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lead_task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lt_lead_id', 'lt_task_id', 'lt_user_id', 'lt_date'], 'required'],
            [['lt_lead_id', 'lt_task_id', 'lt_user_id'], 'integer'],
            [['lt_date', 'lt_completed_dt', 'lt_updated_dt'], 'safe'],
            [['lt_notes'], 'string', 'max' => 500],
            [['lt_lead_id', 'lt_task_id', 'lt_user_id', 'lt_date'], 'unique', 'targetAttribute' => ['lt_lead_id', 'lt_task_id', 'lt_user_id', 'lt_date']],
            [['lt_lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lt_lead_id' => 'id']],
            [['lt_task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::class, 'targetAttribute' => ['lt_task_id' => 't_id']],
            [['lt_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['lt_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'lt_lead_id' => 'Lead ID',
            'lt_task_id' => 'Task ID',
            'lt_user_id' => 'User ID',
            'lt_date' => 'Date',
            'lt_notes' => 'Notes',
            'lt_completed_dt' => 'Completed Dt',
            'lt_updated_dt' => 'Updated Dt',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLtLead()
    {
        return $this->hasOne(Lead::class, ['id' => 'lt_lead_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLtTask()
    {
        return $this->hasOne(Task::class, ['t_id' => 'lt_task_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLtUser()
    {
        return $this->hasOne(Employee::class, ['id' => 'lt_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return LeadTaskQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LeadTaskQuery(get_called_class());
    }

    /**
     * @param int $lead_id
     * @param int $user_id
     * @param int $day
     * @param string $date
     * @return bool
     */
    public static function createTaskList(int $lead_id, int $user_id, int $day = 1, string $date = '')
    {

        if($day < 1) {
            $day = 1;
        }

        if(!$date) {
            $date = date('Y-m-d');
        }

        if($lead_id && $user_id) {
            switch ($day) {
                case 1:
                case 2:
                case 3:
                    $taskList = ['call1', 'call2', 'voice-mail', 'email'];
                    break;
                default:
                    $taskList = [];
            }

            foreach ($taskList as $taskKey) {
                $task = Task::find()->select(['t_id'])->where(['t_key' => $taskKey])->one();
                if($task) {
                    $lt = new LeadTask();
                    $lt->lt_lead_id = $lead_id;
                    $lt->lt_task_id = $task->t_id;
                    $lt->lt_user_id = $user_id;
                    $lt->lt_date = date('Y-m-d', strtotime($date ." +".($day - 1)." days"));
                    if(!$lt->save()) {
                        Yii::error(print_r($lt->errors), 'LeadTask:createTaskList:Task:save');
                    }
                }
            }
            return true;
        }
        return false;
    }
}
