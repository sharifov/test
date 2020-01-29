<?php

namespace modules\invoice\src\entities\invoiceStatusLog;

use common\models\Employee;
use modules\invoice\src\entities\invoice\Invoice;
use modules\invoice\src\entities\invoice\InvoiceStatus;
use modules\invoice\src\entities\invoice\InvoiceStatusAction;
use yii\db\ActiveQuery;

/**
 * @property int $invsl_id
 * @property int $invsl_invoice_id
 * @property int|null $invsl_start_status_id
 * @property int $invsl_end_status_id
 * @property string $invsl_start_dt
 * @property string|null $invsl_end_dt
 * @property int|null $invsl_duration
 * @property string|null $invsl_description
 * @property int|null $invsl_created_user_id
 * @property int|null $invsl_action_id
 *
 * @property Employee $createdUser
 * @property Invoice $invoice
 */
class InvoiceStatusLog extends \yii\db\ActiveRecord
{
    public static function create(CreateDto $dto): self
    {
        $log = new static();
        $log->invsl_invoice_id = $dto->invoiceId;
        $log->invsl_start_status_id = $dto->startStatusId;
        $log->invsl_end_status_id = $dto->endStatusId;
        $log->invsl_start_dt = date('Y-m-d H:i:s');
        $log->invsl_description = $dto->description;
        $log->invsl_action_id = $dto->actionId;
        $log->invsl_created_user_id = $dto->creatorId;
        return $log;
    }

    public function end(): void
    {
        $this->invsl_end_dt = date('Y-m-d H:i:s');
        $this->invsl_duration = (int) (strtotime($this->invsl_end_dt) - strtotime($this->invsl_start_dt));
    }

    public static function tableName(): string
    {
        return '{{%invoice_status_log}}';
    }

    public function rules(): array
    {
        return [
            ['invsl_invoice_id', 'required'],
            ['invsl_invoice_id', 'integer'],
            ['invsl_invoice_id', 'exist', 'skipOnError' => true, 'targetClass' => Invoice::class, 'targetAttribute' => ['invsl_invoice_id' => 'inv_id']],

            ['invsl_start_status_id', 'integer'],
            ['invsl_start_status_id', 'in', 'range' => array_keys(InvoiceStatus::getList())],

            ['invsl_end_status_id', 'required'],
            ['invsl_end_status_id', 'integer'],
            ['invsl_end_status_id', 'in', 'range' => array_keys(InvoiceStatus::getList())],

            ['invsl_start_dt', 'required'],
            ['invsl_start_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['invsl_end_dt', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['invsl_duration', 'integer'],

            ['invsl_description', 'string', 'max' => 255],

            ['invsl_action_id', 'integer'],
            ['invsl_action_id', 'filter', 'filter' => 'intval', 'skipOnEmpty' => true],
            ['invsl_action_id', 'in', 'range' => array_keys(InvoiceStatusAction::getList())],

            ['invsl_created_user_id', 'integer'],
            ['invsl_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['invsl_created_user_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'invsl_id' => 'ID',
            'invsl_invoice_id' => 'Invoice ID',
            'invoice' => 'Invoice',
            'invsl_start_status_id' => 'Start Status',
            'invsl_end_status_id' => 'End Status',
            'invsl_start_dt' => 'Start Dt',
            'invsl_end_dt' => 'End Dt',
            'invsl_duration' => 'Duration',
            'invsl_description' => 'Description',
            'invsl_action_id' => 'Action',
            'invsl_created_user_id' => 'Created User',
        ];
    }

    public function getCreatedUser(): ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'invsl_created_user_id']);
    }

    public function getInvoice(): ActiveQuery
    {
        return $this->hasOne(Invoice::class, ['inv_id' => 'invsl_invoice_id']);
    }

    public static function find(): Scopes
    {
        return new Scopes(static::class);
    }
}
