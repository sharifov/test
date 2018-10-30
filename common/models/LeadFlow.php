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
            [['created'], 'safe'],
            [['employee_id', 'lead_id', 'status'], 'integer'],
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
        $stateFlow = new self();
        $stateFlow->lead_id = $lead->id;
        $stateFlow->status = $lead->status;
        $stateFlow->created = date('Y-m-d H:i:s');
        if (!is_a(\Yii::$app, 'yii\console\Application') &&
            !Yii::$app->user->isGuest &&
            Yii::$app->user->identityClass != 'webapi\models\ApiUser'
        ) {
            $stateFlow->employee_id = Yii::$app->user->identity->getId();
        }
        return $stateFlow->save();
    }
}
