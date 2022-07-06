<?php

namespace src\services\email;

use frontend\models\LeadPreviewEmailForm;
use common\models\Lead;

interface EmailServiceInterface
{
    public function createFromLead(LeadPreviewEmailForm $previewEmailForm, Lead $lead, array $attachments = []);

    public function sendMail($email, array $data = []);
}
