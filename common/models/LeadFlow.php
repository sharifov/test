<?php

namespace common\models;

use sales\entities\AggregateRoot;
use sales\entities\EventTrait;
use Yii;
use yii\helpers\VarDumper;

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
 * @property LeadFlowChecklist[] $leadFlowChecklist
 */
class LeadFlow extends \yii\db\ActiveRecord implements AggregateRoot
{

    use EventTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lead_flow';
    }

    /**
     * @param $leadId
     * @param $oldStatus
     * @param $newStatus
     * @param null $userId
     * @param null $description
     * @return LeadFlow
     */
    public static function create($leadId, $oldStatus, $newStatus, $userId = null, $description = null): self
    {
        $leadFlow = new static();
        $leadFlow->lead_id = $leadId;
        $leadFlow->lf_from_status_id = $oldStatus;
        $leadFlow->status = $newStatus;
        $leadFlow->employee_id = $userId;
        $leadFlow->lf_description = $description;
        $leadFlow->created = date('Y-m-d H:i:s');
        return $leadFlow;
    }

    public function setEndedTime()
    {
        $this->lf_end_dt = date('Y-m-d H:i:s');
        $this->lf_time_duration = (int) (strtotime($this->lf_end_dt) - strtotime($this->created));
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
            'lf_description' => 'Description',
            'leadFlowChecklist' => 'Lead Flow Checklist'
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLeadFlowChecklist()
    {
        return $this->hasMany(LeadFlowChecklist::class, ['lfc_lf_id' => 'id']);
    }

    public static function addStateFlow(Lead $lead)
    {

        $logPrev = self::find()->where(['lead_id' => $lead->id])->orderBy(['id' => SORT_DESC])->limit(1)->one();

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

        //return $stateFlow->save();

        if ($stateFlow->save()) {
            if ($lead->employee_id) {
                if ($checkLists = LeadChecklist::find()
                    ->andWhere(['lc_user_id' => $lead->employee_id, 'lc_lead_id' => $lead->id])
                    ->orderBy(['lc_created_dt' => SORT_ASC])
                    ->all()
                ) {
                    foreach ($checkLists as $checkList) {
                        $leadFlowChecklist = new LeadFlowChecklist();
                        $leadFlowChecklist->lfc_lf_id = $stateFlow->id;
                        $leadFlowChecklist->lfc_lc_type_id = $checkList->lc_type_id;
                        $leadFlowChecklist->lfc_lc_user_id = $checkList->lc_user_id;
                        if (!$leadFlowChecklist->save()) {
                            Yii::error(VarDumper::dumpAsString($leadFlowChecklist->errors), 'LeadFlow:addStateFlow:leadFlowChecklist:save');
                        }
                    }
                }
            }
        } else {
            Yii::error(VarDumper::dumpAsString($stateFlow->errors), 'LeadFlow:addStateFlow:stateFlow:save');
        }
    }

    /**
     * @param Lead2 $lead
     * @param bool $insert
     * @return bool
     */
    public static function addStateFlow2(Lead2 $lead, bool $insert = true): bool
    {

        $stateFlow = new self();
        $stateFlow->lead_id = $lead->id;
        $stateFlow->status = $lead->status;
        $stateFlow->created = date('Y-m-d H:i:s');

        if(!$insert) {
            $logPrev = self::find()->where(['lead_id' => $lead->id])->orderBy(['id' => SORT_DESC])->limit(1)->one();

            if ($logPrev) {
                $logPrev->lf_end_dt = date('Y-m-d H:i:s');
                $logPrev->lf_time_duration = (int)(strtotime($logPrev->lf_end_dt) - strtotime($logPrev->created));
                if($logPrev->save()) {
                    if ($logPrev->status) {
                        $stateFlow->lf_from_status_id = $logPrev->status;
                    }
                }
            }
        }

        /*if($lead->status_description) {
            $stateFlow->lf_description = mb_substr($lead->status_description, 0, 250);
        }*/


        if (!is_a(\Yii::$app, 'yii\console\Application') &&
            !Yii::$app->user->isGuest &&
            Yii::$app->user->identityClass != 'webapi\models\ApiUser'
        ) {
            $stateFlow->employee_id = Yii::$app->user->id;
        }
        return $stateFlow->save();
    }
}
