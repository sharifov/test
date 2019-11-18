<?php

namespace common\models;

use sales\entities\EventTrait;
use Yii;

/**
 * This is the model class for table "notes".
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
class Note extends \yii\db\ActiveRecord
{

    use EventTrait;

    public static function create($userId, $leadId, $message): self
    {
        $note = new static();
        $note->employee_id = $userId;
        $note->lead_id = $leadId;
        $note->message = $message;
        return $note;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['employee_id', 'lead_id'], 'integer'],
            [['message'],'string'],
            [['message'],'required'],
            [['created'], 'safe'],
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
        return $this->hasOne(Employee::class, ['id' => 'employee_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLead()
    {
        return $this->hasOne(Lead::class, ['id' => 'lead_id']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if($this->lead_id && $this->lead) {
            $this->lead->updateLastAction();
        }

    }
}
