<?php

namespace common\models;

use common\models\local\LeadLogMessage;
use Yii;

/**
 * This is the model class for table "lead_logs".
 *
 * @property int $id
 * @property int $employee_id
 * @property int $lead_id
 * @property string $message
 * @property string $created
 *
 * @property Employee $employee
 * @property Lead $lead
 */
class LeadLog extends \yii\db\ActiveRecord
{
    /**
     * @var $mode LeadLogMessage
     */
    public $logMessage;
    public $agent;

    public function __construct(LeadLogMessage $logMessage = null, array $config = [])
    {
        $this->logMessage = $logMessage;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lead_logs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['employee_id', 'lead_id'], 'integer'],
            [['message'], 'string'],
            [['created'], 'safe'],
            [['employee_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::className(), 'targetAttribute' => ['employee_id' => 'id']],
            [['lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::className(), 'targetAttribute' => ['lead_id' => 'id']],
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
            'lead_id' => 'Lead ID',
            'message' => 'Message',
            'created' => 'Created',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmployee()
    {
        return $this->hasOne(Employee::className(), ['id' => 'employee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLead()
    {
        return $this->hasOne(Lead::className(), ['id' => 'lead_id']);
    }

    public function afterFind()
    {
        if ($this->employee_id != null) {
            $this->agent = sprintf('%s (%s)', $this->employee->username, $this->employee->role);
        } else {
            $this->agent = 'System';
        }

        $this->logMessage = new LeadLogMessage();
        $this->logMessage->setAttributes(json_decode($this->message, true));

        parent::afterFind();
    }

    public function addLog($params)
    {
        $this->attributes = $params;
        $this->message = $this->logMessage->getMessage();
        if (!is_a(\Yii::$app, 'yii\console\Application')) {
            $this->employee_id = (Yii::$app->user->isGuest)
                ? null : Yii::$app->user->identity->getId();
        }
        return $this->save();
    }
}
