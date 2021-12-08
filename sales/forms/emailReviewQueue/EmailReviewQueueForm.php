<?php

namespace sales\forms\emailReviewQueue;

use common\models\Email;
use sales\model\emailReviewQueue\entity\EmailReviewQueue;
use yii\base\Model;

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

    public const SCENARIO_SEND_EMAIL = 'sendEmail';
    public const SCENARIO_REJECT = 'reject';

    public function __construct(?Email $email, ?int $emailQueueId, $config = [])
    {
        parent::__construct($config);
        if ($email) {
            $this->emailFrom = $email->e_email_from;
            $this->emailFromName = $email->e_email_from_name;
            $this->emailId = $email->e_id;
            $this->emailTo = $email->e_email_to;
            $this->emailToName = $email->e_email_to_name;
            $this->emailSubject = $email->e_email_subject;
            $this->emailMessage = $email->getEmailBodyHtml();
        }
        $this->emailQueueId = $emailQueueId;
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
            [['emailId', 'emailQueueId'], 'integer'],
            [['emailMessage'], 'string'],
            [['emailSubject'], 'string', 'max' => 200, 'min' => 5],
            [['emailFromName', 'emailToName'], 'string', 'max' => 50],
            [['emailId'], 'exist', 'skipOnError' => true, 'targetClass' => Email::class, 'targetAttribute' => ['emailId' => 'e_id']],
            [['emailQueueId'], 'exist', 'skipOnError' => true, 'targetClass' => EmailReviewQueue::class, 'targetAttribute' => ['emailQueueId' => 'erq_id']],
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
}
