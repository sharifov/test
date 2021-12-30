<?php

namespace modules\fileStorage\src\widgets;

use frontend\models\CasePreviewEmailForm;
use frontend\models\LeadPreviewEmailForm;
use yii\base\Widget;

/**
 * Class FileStorageEmailSendListWidget
 */
class FileStorageEmailSendListWidget extends Widget
{
    public array $files = [];
    public string $checkBoxName;
    public array $selectedFiles = [];

    public function run(): string
    {
        return $this->render('email_list', [
            'files' => $this->files,
            'checkBoxName' => $this->checkBoxName,
            'selectedFiles' => $this->selectedFiles
        ]);
    }

    public static function byLead(array $files): string
    {
        return self::widget([
            'files' => $files,
            'checkBoxName' => (new LeadPreviewEmailForm())->formName()
        ]);
    }

    public static function byCase(array $files): string
    {
        return self::widget([
            'files' => $files,
            'checkBoxName' => (new CasePreviewEmailForm())->formName()
        ]);
    }

    public static function byReview(array $files, string $formName, array $selectedFiles): string
    {
        return self::widget([
            'files' => $files,
            'checkBoxName' => $formName,
            'selectedFiles' => $selectedFiles
        ]);
    }
}
