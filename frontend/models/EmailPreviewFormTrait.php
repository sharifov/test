<?php

namespace frontend\models;

trait EmailPreviewFormTrait
{
    public function getEmailFrom(): string
    {
        return $this->e_email_from;
    }

    public function getEmailTo(): string
    {
        return $this->e_email_to;
    }

    public function getEmailFromName(): ?string
    {
        return $this->e_email_from_name;
    }

    public function getEmailToName(): ?string
    {
        return $this->e_email_to_name;
    }

    public function getEmailSubject(): ?string
    {
        return $this->e_email_subject;
    }

    public function getEmailMessage(): ?string
    {
        return $this->e_email_message;
    }

    public function getEmailTemplateId(): ?int
    {
        return $this->e_email_tpl_id;
    }

    public function getLanguageId(): ?string
    {
        return $this->e_language_id;
    }
}
