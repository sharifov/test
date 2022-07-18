<?php

namespace src\services\email;

use common\models\Lead;
use src\entities\cases\Cases;
use src\forms\emailReviewQueue\EmailReviewQueueForm;
use frontend\models\EmailPreviewFromInterface;
use src\dto\email\EmailDTO;
use src\entities\email\EmailInterface;

interface EmailServiceInterface
{
    public function createFromLead(EmailPreviewFromInterface $previewEmailForm, Lead $lead, array $attachments = []);

    public function createFromCase(EmailPreviewFromInterface $previewEmailForm, Cases $case, array $attachments = []);

    public function sendMail(EmailInterface $email, array $data = []);

    public function updateAfterReview(EmailReviewQueueForm $form, $email);

    public function createFromDTO(EmailDTO $emailDTO, $autoDetectEmpty = true);
}
