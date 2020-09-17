<?php

namespace sales\model\call\entity\callCommand;

use common\models\Employee;
use sales\model\phoneLine\phoneLine\entity\PhoneLine;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "phone_line_command".
 *
 * @property int $plc_id
 * @property int|null $plc_line_id
 * @property int|null $plc_ccom_id
 * @property int|null $plc_sort_order
 * @property int|null $plc_created_user_id
 * @property string|null $plc_created_dt
 *
 * @property CallCommand $callCommand
 * @property Employee $createdUser
 * @property PhoneLine $phoneLine
 */
class PhoneLineCommand extends ActiveRecord
{

    public static function tableName(): string
    {
        return '{{%phone_line_command}}';
    }

    public function rules(): array
    {
        return [
            [['plc_line_id', 'plc_ccom_id', 'plc_sort_order', 'plc_created_user_id'], 'integer'],
            [['plc_created_dt'], 'safe'],
            [['plc_ccom_id'], 'exist', 'skipOnError' => true, 'targetClass' => CallCommand::class, 'targetAttribute' => ['plc_ccom_id' => 'ccom_id']],
            [['plc_created_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['plc_created_user_id' => 'id']],
            [['plc_line_id'], 'exist', 'skipOnError' => true, 'targetClass' => PhoneLine::class, 'targetAttribute' => ['plc_line_id' => 'line_id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'plc_id' => 'ID',
            'plc_line_id' => 'Line ID',
            'plc_ccom_id' => 'Ccom ID',
            'plc_sort_order' => 'Sort Order',
            'plc_created_user_id' => 'Created User ID',
            'plc_created_dt' => 'Created Dt',
        ];
    }

    public function getCallCommand(): ActiveQuery
    {
        return $this->hasOne(CallCommand::class, ['ccom_id' => 'plc_ccom_id']);
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'plc_created_user_id']);
    }

    public function getPhoneLine(): ActiveQuery
    {
        return $this->hasOne(PhoneLine::class, ['line_id' => 'plc_line_id']);
    }
}
