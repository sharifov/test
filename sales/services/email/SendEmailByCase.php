<?php

namespace sales\services\email;

use common\models\Email;
use common\models\EmailTemplateType;
use common\models\Project;
use sales\dto\email\EmailConfigsDTO;
use sales\entities\cases\CaseCategory;
use sales\entities\cases\Cases;
use sales\model\cases\CaseCodeException;
use sales\repositories\NotFoundException;
use yii\helpers\VarDumper;

/**
 * Class SendEmailByCase
 *
 * @property int $case_id
 * @property string $contact_email
 * @property int|null $resultStatus
 */
class SendEmailByCase
{
    public const RESULT_NOT_ENABLE = 0;
    public const RESULT_SEND = 1;

    private int $case_id;
    private string $contact_email;
    private ?int $resultStatus = null;

    /**
     * SendEmailByCase constructor.
     * @param int $case_id
     * @param string $contact_email
     */
    public function __construct(
        int $case_id,
        string $contact_email
    ) {
        $this->case_id = $case_id;
        $this->contact_email = $contact_email;

        $this->resultStatus = $this->handle();
    }

    private function handle(): int
    {
        if (!$case = Cases::findOne($this->case_id)) {
            throw new NotFoundException('Case is not found', CaseCodeException::CASE_NOT_FOUND);
        }

        if (($project = $case->project) && ($category = $case->category)) {
            $emailConfigs = $this->getEmailConfigsDto($project, $category);
            if ($emailConfigs->enabled) {
                $emailTemplate = $this->getEmailTemplate($emailConfigs->templateTypeKey);

                $mailPreview = \Yii::$app->communication->mailPreview($case->cs_project_id, $emailTemplate->etp_key, $emailConfigs->emailFrom, $this->contact_email);
                if ($mailPreview['error'] !== false) {
                    throw new \DomainException($mailPreview['error']);
                }

                $mail = new Email();
                $mail->e_project_id = $case->cs_project_id;
                $mail->e_case_id = $case->cs_id;
                $mail->e_template_type_id = $emailTemplate->etp_id;
                $mail->e_type_id = Email::TYPE_OUTBOX;
                $mail->e_status_id = Email::STATUS_PENDING;
                $mail->e_email_subject = $mailPreview['data']['email_subject'];
                $mail->e_email_from = $emailConfigs->emailFrom;
                $mail->e_email_from_name = $emailConfigs->emailFromName;
                $mail->e_email_to = $this->contact_email;
                $mail->e_created_dt = date('Y-m-d H:i:s');

                $mailResponse = $mail->sendMail();
                if ($mailResponse['error'] !== false) {
                    throw new \DomainException('Email(Id: ' . $mail->e_id . ') has not been sent.');
                }
                return self::RESULT_SEND;
            }
            return self::RESULT_NOT_ENABLE;
        }
        throw new \RuntimeException('Project(' . $case->cs_project_id . ') or CaseCategory(' . $case->cs_category_id . ') not found');
    }

    private function getEmailConfigsDto(Project $project, CaseCategory $caseCategory): EmailConfigsDTO
    {
        $emailConfigs = $project->getEmailConfigOnApiCaseCreate()[$caseCategory->cc_key] ?? null;
        if (!$emailConfigs) {
            \Yii::error(VarDumper::dumpAsString([
                'projectParams' => $project->p_params_json,
                'categoryKey' => $caseCategory->cc_key,
                'emailConfigs' => $emailConfigs
            ]), 'SendEmailOnCaseCreationBOJob::getEmailConfigsDto');
            throw new \RuntimeException('Not Found email configs in project(' . $project->name . ' - ' . $project->id . ') params by case category key (' . $caseCategory->cc_key . ')');
        }
        return new EmailConfigsDTO($emailConfigs);
    }

    private function getEmailTemplate(string $templateTypeKey): EmailTemplateType
    {
        if (!$emailTemplate = EmailTemplateType::findOne(['etp_key' => $templateTypeKey])) {
            throw new \RuntimeException('Not found template type by key (' . $templateTypeKey . ')');
        }
        return $emailTemplate;
    }

    public function getResultStatus(): ?int
    {
        return $this->resultStatus;
    }
}
