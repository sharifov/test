<?php

namespace src\entities\email;

use Yii;
use common\models\Client;
use common\models\Employee;
use common\models\Department;
use common\models\Project;
use src\entities\cases\Cases;
use common\models\Lead;
use yii\behaviors\TimestampBehavior;
use src\behaviors\metric\MetricEmailCounterBehavior;
use yii\db\ActiveRecord;
use src\auth\Auth;
use common\models\Email as EmailOld;

/**
 * This is the model class for table "email_norm".
 *
 * @property int $e_id
 * @property int|null $e_project_id
 * @property int|null $e_departament_id
 * @property int $e_type_id
 * @property int $e_is_deleted
 * @property int $e_status_id
 * @property int|null $e_created_user_id
 * @property string|null $e_created_dt
 * @property string|null $e_updated_dt
 * @property int|null $e_params_id
 * @property int|null $e_body_id
 *
 * @property Employee $createdUser
 * @property Department $departament
 * @property Project $project
 * @property EmailParams $params
 * @property EmailBody $emailBody
 * @property EmailLog $emailLog
 * @property Case[] $cases
 * @property Client[] $clients
 * @property Lead[] $leads
 * @property EmailContact[] $emailContacts
 */
class Email extends ActiveRecord
{
    public const TYPE_DRAFT     = 0;
    public const TYPE_OUTBOX    = 1;
    public const TYPE_INBOX     = 2;

    public const TYPE_LIST = [
        self::TYPE_DRAFT    => 'Draft',
        self::TYPE_OUTBOX   => 'Outbox',
        self::TYPE_INBOX    => 'Inbox',
    ];

    public const STATUS_NEW     = 1;
    public const STATUS_PENDING = 2;
    public const STATUS_PROCESS = 3;
    public const STATUS_CANCEL  = 4;
    public const STATUS_DONE    = 5;
    public const STATUS_ERROR   = 6;
    public const STATUS_REVIEW  = 7;

    public const STATUS_LIST = [
        self::STATUS_NEW        => 'New',
        self::STATUS_PENDING    => 'Pending',
        self::STATUS_PROCESS    => 'Process',
        self::STATUS_CANCEL     => 'Cancel',
        self::STATUS_DONE       => 'Done',
        self::STATUS_ERROR      => 'Error',
        self::STATUS_REVIEW     => 'Review'
    ];

    public function rules(): array
    {
        return [
            ['e_body_id', 'integer'],

            ['e_created_dt', 'safe'],

            ['e_created_user_id', 'integer'],
            ['e_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['e_created_user_id' => 'id']],

            ['e_departament_id', 'integer'],
            ['e_departament_id', 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['e_departament_id' => 'dep_id']],

            ['e_is_deleted', 'integer'],

            ['e_params_id', 'integer'],
            ['e_params_id', 'exist', 'skipOnError' => true, 'targetClass' => EmailParams::class, 'targetAttribute' => ['e_params_id' => 'ep_id']],

            ['e_project_id', 'integer'],
            ['e_project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['e_project_id' => 'id']],

            ['e_status_id', 'integer'],

            ['e_type_id', 'integer'],

            ['e_updated_dt', 'safe'],
        ];
    }

    public function getCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'e_created_user_id']);
    }

    public function getDepartament(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Department::class, ['dep_id' => 'e_departament_id']);
    }

    public function getProject(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Project::class, ['id' => 'e_project_id']);
    }

    public function getParams(): \yii\db\ActiveQuery
    {
        return $this->hasOne(EmailParams::class, ['ep_id' => 'e_params_id']);
    }

    public function getEmailBody(): \yii\db\ActiveQuery
    {
        return $this->hasOne(EmailBody::class, ['embd_id' => 'e_body_id']);
    }

    public function getCases(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Cases::class, ['cs_id' => 'ec_case_id'])->viaTable('email_case', ['ec_email_id' => 'e_id']);
    }

    public function getClients(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Client::class, ['id' => 'ecl_client_id'])->viaTable('email_client', ['ecl_email_id' => 'e_id']);
    }

    public function getLeads(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Lead::class, ['id' => 'el_lead_id'])->viaTable('email_lead', ['el_email_id' => 'e_id']);
    }

    public function getEmailLog(): \yii\db\ActiveQuery
    {
        return $this->hasOne(EmailLog::class, ['el_email_id' => 'e_id']);
    }

    public function attributeLabels(): array
    {
        return [
            'e_id' => 'ID',
            'e_project_id' => 'Project ID',
            'e_departament_id' => 'Departament ID',
            'e_type_id' => 'Type ID',
            'e_is_deleted' => 'Is Deleted',
            'e_status_id' => 'Status ID',
            'e_created_user_id' => 'Created User ID',
            'e_created_dt' => 'Created Dt',
            'e_updated_dt' => 'Updated Dt',
            'e_params_id' => 'Params ID',
            'e_body_id' => 'Body ID',
        ];
    }

    public static function tableName(): string
    {
        return 'email_norm';
    }

    /**
     * @return array
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['e_created_dt'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['e_updated_dt'],
                ],
                'value' => date('Y-m-d H:i:s')
            ],
            'metric' => [
                'class' => MetricEmailCounterBehavior::class,
            ],
        ];
    }

    /**
     * TODO: fix message id using normalized data
     * @return string
     */
    public function generateMessageId(): string
    {
        $arr[] = 'kiv';
        $arr[] = $this->e_id;
        $arr[] = $this->e_project_id;
       // $arr[] = $this->e_lead_id;
      //  $arr[] = $this->e_email_from;
      //  $arr[] = $this->e_case_id;

        $message = '<' . implode('.', $arr) . '>';
        return $message;
    }

    /**
     * @return static
     */
    private static function create(): self
    {
        $email = new static();
        $email->e_created_dt = date('Y-m-d H:i:s');
        $email->e_created_user_id = Auth::employeeId();

        return $email;
    }

    public static function createFromEmailObject(EmailOld $emailOld)
    {
        $email = self::create();
        $email->e_id = $emailOld->e_id;
        $email->e_type_id = $emailOld->e_type_id;
        $email->e_status_id = $emailOld->e_status_id;
        $email->e_is_deleted = $emailOld->e_is_deleted;
        $email->e_project_id = $emailOld->e_project_id;
        $email->e_created_dt = $emailOld->e_created_dt;
        $email->e_created_user_id = $emailOld->e_created_user_id;
        $email->e_updated_dt = $emailOld->e_updated_dt;
        //$email->e_message_id = $email->generateMessageId();

        return $email;
    }
}
