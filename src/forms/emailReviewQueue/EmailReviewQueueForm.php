<?php

namespace src\forms\emailReviewQueue;

use common\components\validators\IsArrayValidator;
use common\models\Email;
use frontend\helpers\JsonHelper;
use modules\fileStorage\src\entity\fileLead\FileLeadQuery;
use modules\fileStorage\src\entity\fileStorage\FileStorage;
use modules\fileStorage\src\services\url\FileInfo;
use src\model\emailReviewQueue\entity\EmailReviewQueue;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use src\entities\email\Email as EmailNorm;

/**
 * Class EmailReviewQueueForm
 * @package frontend\models
 *
 * @property string $emailFrom
 * @property string $emailFromName
 * @property int $emailId
 * @property int|null $emailQueueId
 * @property string $emailTo
 * @property string $emailToName
 * @property string $emailSubject
 * @property string $emailMessage
 * @property int|null $emailIsNorm
 */
class EmailReviewQueueForm extends Model
{
    public $emailFrom;
    public $emailFromName;
    public $emailId;
    public $emailTo;
    public $emailToName;
    public $emailSubject;
    public $emailMessage;
    public $emailQueueId;
    public $emailIsNorm;
    public $files;
    private $fileList;
    private $selectedFiles = [];

    public $leadId;
    public $caseId;

    public const SCENARIO_SEND_EMAIL = 'sendEmail';
    public const SCENARIO_REJECT = 'reject';

    public function __construct($email, ?int $emailQueueId, $config = [])
    {
        parent::__construct($config);
        if ($email) {
            if ($email instanceof Email) {
                $this->fillWithEmail($email);
            } else {
                $this->fillWithEmailNorm($email);
            }
//            $this->selectedFiles = $this->parseSelectedFiles($email->e_email_data);
        }
        $this->emailQueueId = $emailQueueId;
    }

    public function fillWithEmail(Email $email)
    {
        $this->emailFrom = $email->e_email_from;
        $this->emailFromName = $email->e_email_from_name;
        $this->emailId = $email->e_id;
        $this->emailTo = $email->e_email_to;
        $this->emailToName = $email->e_email_to_name;
        $this->emailSubject = $email->e_email_subject;
        $this->emailMessage = $email->getEmailBodyHtml();
        $this->leadId = $email->e_lead_id;
        $this->caseId = $email->e_case_id;

        return $this;
    }

    public function fillWithEmailNorm(EmailNorm $email)
    {
        $this->emailFrom = $email->contactFrom->ea_email;
        $this->emailFromName = $email->contactFrom->ea_name;
        $this->emailId = $email->e_id;
        $this->emailTo = $email->contactTo->ea_email;
        $this->emailToName = $email->contactTo->ea_name;
        $this->emailSubject = $email->emailSubject;
        $this->emailMessage = $email->emailBody->getBodyHtml();
        $this->leadId = $email->lead->id ?? null;
        $this->caseId = $email->case->cs_id ?? null;
        $this->emailIsNorm = 1;

        return $this;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['emailFrom', 'emailTo', 'emailMessage', 'emailSubject', 'emailId', 'emailQueueId'], 'required', 'on' => self::SCENARIO_SEND_EMAIL],
            [['emailQueueId', 'emailId'], 'required', 'on' => self::SCENARIO_REJECT],
            [['emailSubject', 'emailMessage'], 'trim'],
            [['emailTo', 'emailFrom'], 'email'],
            [['emailId', 'emailQueueId', 'leadId', 'caseId', 'emailIsNorm'], 'integer'],
            [['emailMessage'], 'string'],
            [['emailSubject'], 'string', 'max' => 200, 'min' => 5],
            [['emailFromName', 'emailToName'], 'string', 'max' => 50],
            [['emailId'], 'exist', 'skipOnError' => true, 'targetClass' => EmailNorm::class, 'targetAttribute' => ['emailId' => 'e_id'], 'when' => function ($model) {
                return $model->emailIsNorm == 1;
            }],
            [['emailId'], 'exist', 'skipOnError' => true, 'targetClass' => Email::class, 'targetAttribute' => ['emailId' => 'e_id'], 'when' => function ($model) {
                return $model->emailIsNorm === null;
            }],
            [['emailQueueId'], 'exist', 'skipOnError' => true, 'targetClass' => EmailReviewQueue::class, 'targetAttribute' => ['emailQueueId' => 'erq_id']],

            ['files', IsArrayValidator::class],
            ['files', 'each', 'rule' => ['string'], 'skipOnError' => true, 'skipOnEmpty' => true],
        ];
    }

    public function sendEmailScenario(): void
    {
        $this->scenario = self::SCENARIO_SEND_EMAIL;
    }

    public function rejectEmailScenario(): void
    {
        $this->scenario = self::SCENARIO_REJECT;
    }

    public function getFileList(): array
    {
        if ($this->fileList !== null) {
            return $this->fileList;
        }
        $fileLeadQuery = [];
        if ($this->leadId) {
            $fileLeadQuery = FileLeadQuery::getListByLead($this->leadId);
        } else if ($this->caseId) {
            $fileLeadQuery = FileLeadQuery::getListByLead($this->caseId);
        }
        $this->fileList = ArrayHelper::map($fileLeadQuery, 'uid', 'name');
        return $this->fileList = ArrayHelper::map($fileLeadQuery, 'uid', 'name');
    }

    public function getFilesPath(): array
    {
        $files = [];
        if (!$this->files) {
            return $files;
        }
        foreach ($this->files as $fileUid) {
            if ($fileStorage = FileStorage::findOne(['fs_uid' => $fileUid])) {
                $files[] = new FileInfo(
                    $fileStorage->fs_name,
                    $fileStorage->fs_path,
                    $fileStorage->fs_uid,
                    $fileStorage->fs_title,
                    null
                );
            }
        }
        return $files;
    }

    public function parseSelectedFiles(?string $emailData): array
    {
        $data = JsonHelper::decode($emailData);
        $selectedFiles = [];
        if (!empty($data['files'])) {
            foreach ($data['files'] as $file) {
                $selectedFiles[] = $file['uid'];
            }
        }
        return $selectedFiles;
    }

    public function getSelectedFiles(): array
    {
        return $this->selectedFiles;
    }
}
