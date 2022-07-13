<?php

namespace frontend\models;

interface EmailPreviewFromInterface
{
    public function getEmailFrom(): string;
    public function getEmailTo(): string;
    public function getEmailFromName(): ?string;
    public function getEmailToName(): ?string;
    public function getEmailSubject(): ?string;
    public function getEmailMessage(): ?string;
    public function getEmailTemplateId(): ?int;
    public function getLanguageId(): ?string;

    public function countLettersInEmailMessage(): int;
}
