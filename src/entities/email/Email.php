<?php

namespace src\entities\email;

use common\models\Client;
use common\models\Department;
use common\models\EmailTemplateType;
use common\models\Employee;
use common\models\Language;
use common\models\Lead;
use common\models\Project;
use src\behaviors\metric\MetricEmailCounterBehavior;
use src\entities\cases\Cases;
use src\entities\email\events\EmailDeletedEvent;
use src\entities\email\helpers\EmailContactType;
use src\entities\email\helpers\EmailStatus;
use src\entities\email\helpers\EmailType;
use src\entities\EventTrait;
use src\exception\CreateModelException;
use src\model\BaseActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\entities\email\helpers\EmailPriority;

/**
 * This is the model class for table "email_norm".
 *
 * @property int $e_id
 * @property int|null $e_project_id
 * @property int|null $e_departament_id
 * @property int $e_type_id
 * @property bool $e_is_deleted
 * @property int $e_status_id
 * @property int|null $e_created_user_id
 * @property int|null $e_updated_user_id
 * @property string $e_created_dt
 * @property string|null $e_updated_dt
 * @property int|null $e_body_id
 *
 * @property Employee $createdUser
 * @property Employee $updatedUser
 * @property Department $departament
 * @property Project $project
 * @property EmailParams $params
 * @property EmailBody $emailBody
 * @property EmailLog $emailLog
 * @property Cases[] $cases
 * @property Cases $case
 * @property Client[] $clients
 * @property Client $client
 * @property Lead[] $leads
 * @property Lead $lead
 * @property EmailAddress[] $contacts
 * @property EmailAddress $contactFrom
 * @property EmailAddress $contactTo
 * @property EmailAddress[] $contactsTo
 * @property EmailAddress[] $contactsCc
 * @property EmailAddress[] $contactsBcc
 * @property EmailContact $emailContactFrom
 * @property EmailContact $emailContactTo
 * @property EmailContact[] $emailContacts
 * @property Email $reply
 * @property EmailTemplateType $templateType
 *
 * @property int $id
 * @property array $leadsIds
 * @property array $casesIds
 * @property array $clientsIds
 * @property int|null $leadId
 * @property int|null $caseId
 * @property int|null $clientId
 * @property int|null $projectId
 * @property int|null $templateTypeId
 * @property int|null $priority
 * @property string|null $templateTypeName
 * @property string|null $emailFrom
 * @property string|null $emailFromName
 * @property string|null $emailTo
 * @property string|null $emailToName
 * @property array $emailsTo
 * @property array $emailsCc
 * @property array $emailsBcc
 * @property string|null $emailSubject
 * @property int|null $communicationId
 * @property string|null $languageId
 * @property int|null $departmentId
 * @property string|null $emailBodyHtml
 * @property array|null $emailData
 * @property string|null $errorMessage
 * @property string|null $hash
 * @property string|null $messageId
 * @property string|null $statusDoneDt
 * @property string $statusName
 * @property string $typeName
 *
 */
class Email extends BaseActiveRecord implements EmailInterface
{
    use EventTrait;

    public function rules(): array
    {
        return [
            ['e_created_dt', 'safe'],
            ['e_created_dt', 'required'],
            [['e_body_id', 'e_project_id', 'e_status_id', 'e_type_id', 'e_is_deleted', 'e_created_user_id', 'e_updated_user_id', 'e_departament_id'], 'integer'],
            ['e_is_deleted', 'boolean'],
            ['e_created_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['e_created_user_id' => 'id']],
            ['e_updated_user_id', 'exist', 'skipOnError' => true, 'targetClass' => Employee::class, 'targetAttribute' => ['e_updated_user_id' => 'id']],
            ['e_departament_id', 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['e_departament_id' => 'dep_id']],
            ['e_project_id', 'exist', 'skipOnError' => true, 'targetClass' => Project::class, 'targetAttribute' => ['e_project_id' => 'id']],
            ['e_updated_dt', 'safe'],
        ];
    }

    public function getCreatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'e_created_user_id']);
    }

    public function getUpdatedUser(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Employee::class, ['id' => 'e_updated_user_id']);
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

    //first case
    public function getCase(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Cases::class, ['cs_id' => 'ec_case_id'])->viaTable('email_case', ['ec_email_id' => 'e_id']);
    }

    //first Client
    public function getClient(): \yii\db\ActiveQuery
    {
        return $this->hasOne(Client::class, ['id' => 'ecl_client_id'])->viaTable('email_client', ['ecl_email_id' => 'e_id']);
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

    public function getHash(): ?string
    {
        return $this->emailBody->embd_hash;
    }

    public function getContacts(): \yii\db\ActiveQuery
    {
        return $this->hasMany(EmailAddress::class, ['ea_id' => 'ec_address_id'])->viaTable('email_contact', ['ec_email_id' => 'e_id']);
    }

    public function getEmailContacts(): \yii\db\ActiveQuery
    {
        return $this->hasMany(EmailContact::class, ['ec_email_id' => 'e_id']);
    }

    public function getEmailContactFrom(): \yii\db\ActiveQuery
    {
        return $this->hasOne(EmailContact::class, ['ec_email_id' => 'e_id'])->onCondition(['ec_type_id' => EmailContactType::FROM]);
    }

    public function getEmailContactTo(): \yii\db\ActiveQuery
    {
        return $this->hasOne(EmailContact::class, ['ec_email_id' => 'e_id'])->onCondition(['ec_type_id' => EmailContactType::TO]);
    }

    public function getContactFrom(): \yii\db\ActiveQuery
    {
        return $this->hasOne(EmailAddress::class, ['ea_id' => 'ec_address_id'])
            ->viaTable(
                'email_contact',
                ['ec_email_id' => 'e_id'],
                function ($query) {
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
                function ($query) {
                    $query->onCondition(['ec_type_id' => EmailContactType::TO]);
                }
            );
    }

    public function getContactsTo(): \yii\db\ActiveQuery
    {
        return $this->hasMany(EmailAddress::class, ['ea_id' => 'ec_address_id'])
            ->viaTable(
                'email_contact',
                ['ec_email_id' => 'e_id'],
                function ($query) {
                    $query->onCondition(['ec_type_id' => EmailContactType::TO]);
                }
            );
    }

    public function getContactsCc(): \yii\db\ActiveQuery
    {
        return $this->hasMany(EmailAddress::class, ['ea_id' => 'ec_address_id'])
            ->viaTable(
                'email_contact',
                ['ec_email_id' => 'e_id'],
                function ($query) {
                    $query->onCondition(['ec_type_id' => EmailContactType::CC]);
                }
            );
    }

    public function getContactsBcc(): \yii\db\ActiveQuery
    {
        return $this->hasMany(EmailAddress::class, ['ea_id' => 'ec_address_id'])
            ->viaTable(
                'email_contact',
                ['ec_email_id' => 'e_id'],
                function ($query) {
                    $query->onCondition(['ec_type_id' => EmailContactType::BCC]);
                }
            );
    }

    public function getEmailFrom($masking = true): ?string
    {
        if ($this->contactFrom) {
            return $this->contactFrom->getEmail(EmailType::isInbox($this->e_type_id) && $masking) ?? null;
        }
        return null;
    }

    public function getEmailTo($masking = true): ?string
    {
        if ($this->contactTo) {
            return $this->contactTo->getEmail(EmailType::isOutbox($this->e_type_id) && $masking) ?? null;
        }
        return null;
    }

    public function getEmailsByType(int $type): array
    {
        switch ($type) {
            case EmailContactType::CC:
                return $this->emailsCc;
            case EmailContactType::BCC:
                return $this->emailsBcc;
            case EmailContactType::TO:
                return $this->emailsTo;
            case EmailContactType::FROM:
                $emailFrom = $this->getEmailFrom(false);
                return ($emailFrom !== null) ? [$emailFrom] : [];
        }
        return [];
    }

    public function getEmailsTo(): array
    {
        $emails = [];
        if ($this->contactsTo) {
            foreach ($this->contactsTo as $contact) {
                $emails[] = $contact->getEmail(false);
            }
        }

        return $emails;
    }

    public function getEmailsCc(): array
    {
        $emails = [];
        if ($this->contactsCc) {
            foreach ($this->contactsCc as $contact) {
                $emails[] = $contact->getEmail(false);
            }
        }

        return $emails;
    }

    public function getEmailsBcc(): array
    {
        $emails = [];
        if ($this->contactsBcc) {
            foreach ($this->contactsBcc as $contact) {
                $emails[] = $contact->getEmail(false);
            }
        }

        return $emails;
    }

    public function getEmailFromName(): ?string
    {
        return $this->contactFrom->ea_name ?? null;
    }

    public function getEmailToName(): ?string
    {
        return $this->contactTo->ea_name ?? null;
    }

    public function getCommunicationId(): ?int
    {
        return $this->emailLog->el_communication_id ?? null;
    }

    public function getStatusName(): string
    {
        return EmailStatus::getName($this->e_status_id) ?? '-';
    }

    public function getTypeName(): string
    {
        return EmailType::getName($this->e_type_id) ?? '-';
    }

    public function getEmailSubject(): ?string
    {
        return $this->emailBody->embd_email_subject;
    }

    public function getEmailData()
    {
        return $this->emailBody->getEmailData();
    }

    public function getEmailBodyHtml(): ?string
    {
        return $this->emailBody->getBodyHtml() ?? '';
    }

    public function getTemplateTypeName(): ?string
    {
        return $this->templateType->etp_name ?? null;
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
            $this->recordEvent(new EmailDeletedEvent($emailBody, $this->e_id));
        }
    }

    public function isDeleted(): bool
    {
        return $this->e_is_deleted;
    }

    public function isNew(): bool
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
            'e_updated_user_id' => 'Updated User ID',
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

    //TODO: maybe move to event
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!empty($this->leads)) {
            foreach ($this->leads as $lead) {
                $lead->updateLastAction(LeadPoorProcessingLogStatus::REASON_EMAIL);
            }
        }
        if (!empty($this->cases)) {
            foreach ($this->cases as $case) {
                $case->updateLastAction();
            }
        }
    }

    /**
     *
     * @param string $errorMessage
     * @return Email
     */
    public function statusToError(string $errorMessage)
    {
        $this->saveEmailLog(['el_error_message' => $errorMessage]);
        $this->updateAttributes(['e_status_id' => EmailStatus::ERROR]);

        return $this;
    }

    /**
     *
     * @param string $errorMessage
     * @return Email
     */
    public function statusToCancel(string $errorMessage)
    {
        $this->saveEmailLog(['el_error_message' => $errorMessage]);
        $this->updateAttributes(['e_status_id' => EmailStatus::CANCEL]);

        return $this;
    }

    public function statusToReview()
    {
        $this->updateAttributes(['e_status_id' => EmailStatus::REVIEW]);
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

    public function updateEmailData($emailData)
    {
        $this->emailBody->updateAttributes(['embd_email_data' => $emailData]);
        return $this;
    }

    public function read(): void
    {
        if ($this->emailLog && $this->isNew()) {
            $this->saveEmailLog([
                'el_is_new' => false,
                'el_read_dt' => date('Y-m-d H:i:s')
            ]);
        }
    }

    public function saveInboxId(int $inboxId): void
    {
        $this->saveEmailLog([
            'el_inbox_email_id' => $inboxId
        ]);
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

    public function hasLead(): bool
    {
        return $this->lead !== null;
    }

    public function hasCase(): bool
    {
        return $this->case !== null;
    }

    public function hasClient(): bool
    {
        return $this->client !== null;
    }

    public function getProjectId(): ?int
    {
        return $this->e_project_id;
    }

    public function getDepartmentId(): ?int
    {
        return $this->e_departament_id;
    }

    public function getTemplateTypeId(): ?int
    {
        return $this->templateType->etp_id ?? null;
    }

    public function getLeadId(): ?int
    {
        return $this->lead->id ?? null;
    }

    public function getCaseId(): ?int
    {
        return $this->case->cs_id ?? null;
    }

    public function getClientId(): ?int
    {
        return $this->client->id ?? null;
    }

    public function getLanguageId(): ?string
    {
        return $this->params->ep_language_id ?? null;
    }

    public function getPriority(): ?int
    {
        return $this->params->ep_priority ?? EmailPriority::NORMAL;
    }

    public function getStatusDoneDt(): ?string
    {
        return $this->emailLog->el_status_done_dt ?? null;
    }

    public function getErrorMessage(): ?string
    {
        return $this->emailLog->el_error_message ?? null;
    }

    public function isCreatedUser(int $userId): bool
    {
        return $this->e_created_user_id === $userId;
    }

    public function hasCreatedUser(): bool
    {
        return $this->e_created_user_id ? true : false;
    }

    public function load($data, $formName = null)
    {
        parent::load($data, $formName);

        if (isset($data['e_id'])) {
            $this->e_id = $data['e_id'];
        }
        if (isset($data['e_created_dt'])) {
            $this->e_created_dt = $data['e_created_dt'];
            $this->detachBehavior('timestamp');
        }
        if (isset($data['e_updated_dt'])) {
            $this->e_updated_dt = $data['e_updated_dt'];
            $this->detachBehavior('timestamp');
        }
    }

    public function getId()
    {
        return $this->e_id;
    }

    /**
     *
     * @param array $leadsIds
     * @return array
     */
    public function linkLeads(array $leadsIds): array
    {
        $toUnlink = $this->leadsIds;
        $linked = [];
        foreach ($leadsIds as $id) {
            $key = array_search($id, $this->leadsIds);
            if ($key !== false) {
                unset($toUnlink[$key]);
            } elseif ($lead = Lead::findOne($id)) {
                $this->link('leads', $lead);
                $linked[] = $id;
            }
        }
        if (!empty($toUnlink)) {
            $this->unlinkLeads($toUnlink);
        }
        return $linked;
    }

    public function unlinkLeads(array $leadsIds): void
    {
        foreach ($leadsIds as $id) {
            if ($lead = Lead::findOne($id)) {
                $this->unlink('leads', $lead, true);
            }
        }
    }

    /**
     *
     * @param array $casesIds
     * @return array
     */
    public function linkCases(array $casesIds): array
    {
        $toUnlink = $this->casesIds;
        $linked = [];
        foreach ($casesIds as $id) {
            $key = array_search($id, $this->casesIds);
            if ($key !== false) {
                unset($toUnlink[$key]);
            } elseif ($case = Cases::findOne($id)) {
                $this->link('cases', $case);
                $linked[] = $id;
            }
        }
        if (!empty($toUnlink)) {
            $this->unlinkCases($toUnlink);
        }
        return $linked;
    }

    public function unlinkCases(array $casesIds): void
    {
        foreach ($casesIds as $id) {
            if ($case = Cases::findOne($id)) {
                $this->unlink('cases', $case, true);
            }
        }
    }

    /**
     *
     * @param array $clientsIds
     * @return array
     */
    public function linkClients(array $clientsIds): array
    {
        $toUnlink = $this->clientsIds;
        $linked = [];
        foreach ($clientsIds as $id) {
            $key = array_search($id, $this->clientsIds);
            if ($key !== false) {
                unset($toUnlink[$key]);
            } elseif ($client = Client::findOne($id)) {
                $this->link('clients', $client);
                $linked[] = $id;
            }
        }
        if (!empty($toUnlink)) {
            $this->unlinkClients($toUnlink);
        }
        return $linked;
    }

    public function unlinkClients(array $clientsIds): void
    {
        foreach ($clientsIds as $id) {
            if ($client = Client::findOne($id)) {
                $this->unlink('clients', $client, true);
            }
        }
    }

    /**
     *
     * @param int $replyId
     * @return bool
     */
    public function linkReply(?int $replyId): void
    {
        $linkedToReply = $this->reply;
        if ($reply = self::findOne($replyId)) {
            $this->link('reply', $reply);
        }
        if ($replyId == null || ($linkedToReply != null && !$linkedToReply->equals($reply))) {
            $this->unlink('reply', $linkedToReply, true);
        }
    }

    public function getEmailsContactsIndexedByEmail(int $type): array
    {
        $emailContacts = EmailContact::find()
            ->joinWith('address')
            ->byType($type)
            ->byEmail($this->e_id)
            ->all();

        return ArrayHelper::index($emailContacts, 'address.ea_email');
    }

    public function addContact(int $type, string $email, ?string $name = null): void
    {
        $emails = $this->getEmailsByType($type);
        if (empty($emails) || !in_array($email, $emails)) {
            $address = EmailAddress::findOrNew($email, $name, !empty($name));
            EmailContact::create([
                'ec_address_id' => $address->ea_id,
                'ec_email_id' => $this->e_id,
                'ec_type_id' => $type
            ]);
        }
    }

    public function removeContact(int $type, string $email): void
    {
        $emailContacts = $this->getEmailsContactsIndexedByEmail($type);
        if ($contact = $emailContacts[$email]) {
            $contact->delete();
        }
    }
}
