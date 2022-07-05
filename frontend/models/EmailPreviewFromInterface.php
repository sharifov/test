<?php

namespace frontend\models;

interface EmailPreviewFromInterface
{
    public function countLettersInEmailMessage(): int;
}
