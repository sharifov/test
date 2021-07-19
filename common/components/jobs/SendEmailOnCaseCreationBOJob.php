<?php

namespace common\components\jobs;

use common\models\Email;
use common\models\EmailTemplateType;
use common\models\Project;
use sales\dto\email\EmailConfigsDTO;
use sales\entities\cases\CaseCategory;
use sales\helpers\app\AppHelper;
use sales\repositories\cases\CasesRepository;
use yii\queue\JobInterface;

/**
 * Class SendEmailOnCaseCreationBOJob
 * @package common\components\jobs
 *
 * @property int $case_id
 * @property string $contact_email
 */
class SendEmailOnCaseCreationBOJob extends BaseJob implements JobInterface
{
    public $case_id;

    public $contact_email;

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        try {
            $caseRepository = \Yii::createObject(CasesRepository::class);
            $case = $caseRepository->find($this->case_id);

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
                }
                return true;
            }
            throw new \RuntimeException('Project(' . $case->cs_project_id . ') or CaseCategory(' . $case->cs_category_id . ') not found');
        } catch (\Throwable $e) {
            \Yii::error(AppHelper::throwableLog($e), 'SendEmailOnCaseCreationBOJob::Throwable');
        }
    }

    private function getEmailConfigsDto(Project $project, CaseCategory $caseCategory): EmailConfigsDTO
    {
        $emailConfigs = $project->getEmailConfigOnApiCaseCreate()[$caseCategory->cc_key] ?? null;
        if (!$emailConfigs) {
            throw new \RuntimeException('Not Found email configs in project params by case category key (' . $caseCategory->cc_key . ')');
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
}
