<?php

namespace src\entities\email;

use common\models\Client;
use common\models\Department;
use common\models\Email as EmailOld;
use common\models\EmailTemplateType;
use common\models\Employee;
use common\models\Language;
use common\models\Lead;
use common\models\Project;
use src\auth\Auth;
use src\behaviors\metric\MetricEmailCounterBehavior;
use src\entities\cases\Cases;
use src\entities\email\events\EmailDeletedEvent;
use src\entities\email\helpers\EmailContactType;
use src\entities\email\helpers\EmailStatus;
use src\entities\email\helpers\EmailType;
use src\entities\EventTrait;
use Yii;
use yii\helpers\ArrayHelper;
use common\components\CommunicationService;
use src\services\abtesting\email\EmailTemplateOfferABTestingService;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use src\model\leadUserData\repository\LeadUserDataRepository;
use yii\helpers\VarDumper;
use src\exceptions\CreateModelException;
use src\model\BaseActiveRecord;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

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
 * @property Lead $lead
 * @property EmailAddress[] $contacts
 * @property EmailAddress $contactFrom
 * @property EmailAddress $contactTo
 * @property EmailContact $emailContactFrom
 * @property EmailContact $emailContactTo
 * @property Email $reply
 * @property EmailTemplateType $templateType
 */
class Email extends BaseActiveRecord
{
    use EventTrait;

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
        return $this->hasOne(EmailParams::class, ['ep_email_id' => 'e_id']);
    }

    public function getEmailLog(): \yii\db\ActiveQuery
    {
        return $this->hasOne(EmailLog::class, ['el_email_id' => 'e_id']);
    }

    public function getLanguage(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Language::class, ['language_id' => 'ep_language_id'])->viaTable('email_params', ['ep_email_id' => 'e_id']);
    }

    public function getTemplateType(): \yii\db\ActiveQuery
    {
        return $this->hasOne(EmailTemplateType::class, ['etp_id' => 'ep_template_type_id'])->viaTable('email_params', ['ep_email_id' => 'e_id']);
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

    //first Lead
    public function getLead(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Lead::class, ['id' => 'el_lead_id'])->viaTable('email_lead', ['el_email_id' => 'e_id']);
    }

    public function getLeads(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Lead::class, ['id' => 'el_lead_id'])->viaTable('email_lead', ['el_email_id' => 'e_id']);
    }

    public function getReply(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Email::class, ['e_id' => 'er_reply_id'])->viaTable('email_relation', ['er_email_id' => 'e_id']);
    }

    public function getLeadsIds(): array
    {
        return ArrayHelper::map($this->leads, 'id', 'id');
    }

    public function getClientsIds(): array
    {
        return ArrayHelper::map($this->clients, 'id', 'id');
    }

    public function getCasesIds(): array
    {
        return ArrayHelper::map($this->cases, 'cs_id', 'cs_id');
    }

    public function getHash(): array
    {
        return $this->emailBody->embd_hash;
    }

    public function getContacts(): \yii\db\ActiveQuery
    {
        return $this->hasMany(EmailAddress::class, ['ea_id' => 'ec_address_id'])->viaTable('email_contact', ['ec_email_id' => 'e_id']);
    }

    public function getEmailContactFrom()
    {
        return $this->hasOne(EmailContact::class, ['ec_email_id' => 'e_id'])->onCondition(['ec_type_id' => EmailContactType::FROM]);
    }

    public function getEmailContactTo()
    {
        return $this->hasOne(EmailContact::class, ['ec_email_id' => 'e_id'])->onCondition(['ec_type_id' => EmailContactType::TO]);
    }

    public function getContactFrom(): \yii\db\ActiveQuery
    {
        return $this->hasOne(EmailAddress::class, ['ea_id' => 'ec_address_id'])
            ->viaTable(
                'email_contact',
                ['ec_email_id' => 'e_id'],
                function($query) {
                    $query->onCondition(['ec_type_id' => EmailContactType::FROM]);
                }
            );
    }

    public function getContactTo(): \yii\db\ActiveQuery
    {
        return $this->hasOne(EmailAddress::class, ['ea_id' => 'ec_address_id'])
        ->viaTable(
            'email_contact',
            ['ec_email_id' => 'e_id'],
            function($query) {
                $query->onCondition(['ec_type_id' => EmailContactType::TO]);
            }
            );
    }

    public function getEmailFrom(): string
    {
        return $this->contactFrom->getEmail(EmailType::isInbox($this->e_type_id));
    }

    public function getEmailTo(): string
    {
        return $this->contactTo->getEmail(EmailType::isOutbox($this->e_type_id)) ?? null;
    }

    public function getCommunicationId()
    {
        return $this->emailLog->el_communication_id ?? null;
    }

    public function getStatusName(): string
    {
        return EmailStatus::getName($this->e_status_id);
    }

    public function getEmailSubject(): string
    {
        return $this->emailBody->embd_email_subject;
    }

    /**
     * @return EmailQuery
     */
    public static function find(): EmailQuery
    {
        return new EmailQuery(get_called_class());
    }

    public function delete()
    {
        $emailBody = $this->emailBody;
        if (parent::delete()) {
            $this->recordEvent(new EmailDeletedEvent($emailBody));
        }
    }

    public function isDeleted()
    {
        return $this->e_is_deleted;
    }

    public function isNew()
    {
        return $this->emailLog->el_is_new ?? false;
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
            'e_body_id' => 'Body ID',
            'reply.e_id' => 'Reply ID',
            'contactFrom.ea_name' => 'From Name',
            'contactTo.ea_name' => 'To Name',
            'templateType.etp_name' => 'Template Name',
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
     *
     * @param unknown $errorMessage
     * @return \src\entities\email\Email
     */
    public function statusToError($errorMessage)
    {
        $this->saveEmailLog(['el_error_message' => $errorMessage]);
        $this->updateAttributes(['e_status_id' => EmailStatus::ERROR]);

        return $this;
    }

    /**
     *
     * @param array $attributes
     * @return \src\entities\email\Email
     * @throws \src\exception\CreateModelException
     */
    public function saveEmailLog(array $attributes)
    {
        if (!$this->emailLog) {
            $attributes = array_merge($attributes, ['el_email_id' => $this->e_id]);
            EmailLog::create($attributes);
        } else {
            $this->emailLog->updateAttributes($attributes);
        }

        return $this;
    }

    /**
     *
     * @param array $attributes
     * @return \src\entities\email\Email
     * @throws \src\exception\CreateModelException
     */
    public function saveParams(array $attributes)
    {
        if (!$this->params) {
            $attributes = array_merge($attributes, ['ep_email_id' => $this->e_id]);
            EmailParams::create($attributes);
        } else {
            $this->params->updateAttributes($attributes);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function generateMessageId(): string
    {
        $arr[] = 'kiv';
        $arr[] = $this->e_id;
        $arr[] = $this->e_project_id;
        $arr[] = join('_', $this->getLeadsIds());
        $arr[] = $this->getEmailFrom();
        $arr[] = join('_', $this->getCasesIds());

        $message = '<' . implode('.', $arr) . '>';
        return $message;
    }

    public function getMessageId()
    {
        return $this->emailLog ? $this->emailLog->el_message_id : null;
    }

    public function setMessageId()
    {
        return $this->saveEmailLog(['el_message_id' => $this->generateMessageId()]);
    }

    public static function createFromEmailObject(EmailOld $emailOld)
    {
        $email = self::findOneOrNew(['e_id' => $emailOld->e_id]);

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
