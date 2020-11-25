<?php

namespace sales\model\call\entity\callCommand;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "call_gather_switch".
 *
 * @property int $cgs_ccom_id
 * @property int $cgs_step
 * @property int $cgs_case
 * @property int $cgs_exec_ccom_id
 *
 * @property CallCommand $callCommand
 * @property CallCommand $callExecCommand
 */
class CallGatherSwitch extends ActiveRecord
{


    public static function tableName(): string
    {
        return '{{%call_gather_switch}}';
    }

    public function rules(): array
    {
        return [
            [['cgs_ccom_id', 'cgs_step', 'cgs_case', 'cgs_exec_ccom_id'], 'required'],
            [['cgs_ccom_id', 'cgs_step', 'cgs_case', 'cgs_exec_ccom_id'], 'integer'],
            [['cgs_ccom_id', 'cgs_step', 'cgs_case'], 'unique', 'targetAttribute' => ['cgs_ccom_id', 'cgs_step', 'cgs_case']],
            [['cgs_ccom_id'], 'exist', 'skipOnError' => true, 'targetClass' => CallCommand::class, 'targetAttribute' => ['cgs_ccom_id' => 'ccom_id']],
            [['cgs_exec_ccom_id'], 'exist', 'skipOnError' => true, 'targetClass' => CallCommand::class, 'targetAttribute' => ['cgs_exec_ccom_id' => 'ccom_id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'cgs_ccom_id' => 'Ccom ID',
            'cgs_step' => 'Step',
            'cgs_case' => 'Case',
            'cgs_exec_ccom_id' => 'Exec Ccom ID',
        ];
    }

    public function getCallCommand(): ActiveQuery
    {
        return $this->hasOne(CallCommand::class, ['ccom_id' => 'cgs_ccom_id']);
    }

    public function getCallExecCommand(): ActiveQuery
    {
        return $this->hasOne(CallCommand::class, ['ccom_id' => 'cgs_exec_ccom_id']);
    }
}
