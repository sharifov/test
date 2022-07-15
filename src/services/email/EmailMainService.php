<?php

namespace src\services\email;

use modules\featureFlag\FFlag;
use Yii;
use frontend\models\EmailPreviewFromInterface;
use common\models\Lead;
use src\entities\cases\Cases;
use src\forms\emailReviewQueue\EmailReviewQueueForm;
use src\dto\email\EmailDTO;

/**
 *
 * Class EmailMainService
 *
 * @property EmailService $oldService
 * @property EmailsNormalizeService $normalizedService
 *
 */
class EmailMainService implements EmailServiceInterface
{
    private $oldService;
    private $normalizedService;

    public function __construct()
    {
        $this->oldService = Yii::createObject(EmailService::class);
        $this->normalizedService = Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_EMAIL_NORMALIZED_FORM_ENABLE) ?
            EmailsNormalizeService::newInstance() :
            null
        ;
    }

    public static function newInstance()
    {
        return new static();
    }

    public function sendMail($email, array $data = [])
    {
        if ($this->normalizedService !== null) {
            $this->normalizedService->sendMail($email, $data);
        } else {
            $this->oldService->sendMail($email, $data);
            //TODO: if called norm service need to change status,error message to older version. So need to devide method to smaller parts
        }
    }

    public function createFromLead(EmailPreviewFromInterface $previewEmailForm, Lead $lead, array $attachments = [])
    {
        $email = $this->oldService->createFromLead($previewEmailForm, $lead, $attachments);

        if ($this->normalizedService !== null) {
            $email = $this->normalizedService->createFromLead($previewEmailForm, $lead, $attachments);
        }

        return $email;
    }

    public function createFromCase(EmailPreviewFromInterface $previewEmailForm, Cases $case, array $attachments = [])
    {
        $email = $this->oldService->createFromCase($previewEmailForm, $case, $attachments);

        if ($this->normalizedService !== null) {
            $email = $this->normalizedService->createFromCase($previewEmailForm, $case, $attachments);
        }

        return $email;
    }


    public function updateAfterReview(EmailReviewQueueForm $form, $email)
    {
        $email = $this->oldService->updateAfterReview($form, $email);

        if ($this->normalizedService !== null) {
            $email = $this->normalizedService->updateAfterReview($form, $email);
        }

        return $email;
    }

    public function createFromDTO(EmailDTO $emailDTO, $autoDetectEmpty = true)
    {
        $email = $this->oldService->createFromDTO($emailDTO, $autoDetectEmpty);

        if ($this->normalizedService !== null) {
            $email = $this->normalizedService->createFromDTO($emailDTO, $autoDetectEmpty);
        }

        return $email;
    }
}
