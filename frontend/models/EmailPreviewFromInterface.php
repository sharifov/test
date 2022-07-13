<?php

namespace frontend\models;

interface EmailPreviewFromInterface
{
    public $e_email_from;
    public $e_email_to;
    public $e_email_from_name;
    public $e_email_to_name;
    public $e_email_subject;
    public $e_email_message;
    public $e_email_tpl_id;
    public $e_language_id;

    public function countLettersInEmailMessage(): int;
}
