<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "lead_flow".
 *
 * @property int $id
 * @property string $created
 * @property int $employee_id
 * @property int $lead_id
 * @property int $status
 * @property int $lf_from_status_id
 * @property string $lf_end_dt
 * @property int $lf_time_duration
 * @property int $lf_description
 *
 * @property Employee $employee
 * @property Lead $lead
 */
class LeadFlow extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lead_flow';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created', 'lf_end_dt'], 'safe'],
            [['employee_id', 'lead_id', 'status', 'lf_from_status_id', 'lf_time_duration'], 'integer'],
            [['lf_description'], 'string', 'max' => 250],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['employee_id' => 'id']],
            [['lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lead_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created' => 'Created',
            'employee_id' => 'Employee ID',
            'lead_id' => 'Lead ID',
            'status' => 'Status',
            'lf_from_status_id' => 'From Status',
            'lf_end_dt' => 'End DateTime',
            'lf_time_duration' => 'Duration',
            'lf_description' => 'Description'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['id' => 'employee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLead()
    {
        return $this->hasOne(Lead::class, ['id' => 'lead_id']);
    }

    public static function addStateFlow(Lead $lead)
    {

        $logPrev = \common\models\LeadFlow::find()->where(['lead_id' => $lead->id])->orderBy(['id' => SORT_DESC])->one();

        if($logPrev) {
            $logPrev->lf_end_dt = date('Y-m-d H:i:s');
            $logPrev->lf_time_duration = (int) (strtotime($logPrev->lf_end_dt) - strtotime($logPrev->created));
            $logPrev->save();
        }


        $stateFlow = new self();
        $stateFlow->lead_id = $lead->id;
        $stateFlow->status = $lead->status;
        $stateFlow->created = date('Y-m-d H:i:s');

        if($logPrev && $logPrev->status) {
            $stateFlow->lf_from_status_id = $logPrev->status;
        }

        if($lead->status_description) {
            $stateFlow->lf_description = mb_substr($lead->status_description, 0, 250);
        }


        if (!is_a(\Yii::$app, 'yii\console\Application') &&
            !Yii::$app->user->isGuest &&
            Yii::$app->user->identityClass != 'webapi\models\ApiUser'
        ) {
            $stateFlow->employee_id = Yii::$app->user->id;
        }
        return $stateFlow->save();
    }
}
