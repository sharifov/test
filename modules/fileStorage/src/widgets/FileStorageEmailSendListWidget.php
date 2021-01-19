<?php

namespace modules\fileStorage\src\widgets;

use frontend\models\CasePreviewEmailForm;
use frontend\models\LeadPreviewEmailForm;
use modules\fileStorage\FileStorageSettings;
use yii\base\Widget;

/**
 * Class FileStorageEmailSendListWidget
 */
class FileStorageEmailSendListWidget extends Widget
{
    public array $files = [];
    public string $checkBoxName;

    public function run(): string
    {
        return $this->render('email_list', [
            'files' => $this->files,
            'checkBoxName' => $this->checkBoxName
        ]);
    }

    public static function byLead(array $files): string
    {
        if (!FileStorageSettings::canEmailAttach()) {
            return '';
        }
        return self::widget([
            'files' => $files,
            'checkBoxName' => (new LeadPreviewEmailForm())->formName()
        ]);
    }

    public static function byCase(array $files): string
    {
        if (!FileStorageSettings::canEmailAttach()) {
            return '';
        }
        return self::widget([
            'files' => $files,
            'checkBoxName' => (new CasePreviewEmailForm())->formName()
        ]);
    }
}
