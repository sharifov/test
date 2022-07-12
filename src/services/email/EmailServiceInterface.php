<?php

namespace src\services\email;

use frontend\models\LeadPreviewEmailForm;
use common\models\Lead;
use src\entities\cases\Cases;
use frontend\models\CasePreviewEmailForm;

interface EmailServiceInterface
{
    public function createFromLead(LeadPreviewEmailForm $previewEmailForm, Lead $lead, array $attachments = []);

    public function createFromCase(CasePreviewEmailForm $previewEmailForm, Cases $case, array $attachments = []);

    public function sendMail($email, array $data = []);

    public function sendAfterReview(EmailReviewQueueForm $form, $email);
}
