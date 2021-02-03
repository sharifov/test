<?php

namespace frontend\helpers;

use common\models\Email;
use modules\fileStorage\src\widgets\FileStorageEmailSentListWidget;
use Yii;
use yii\helpers\Html;

class EmailHelper
{
    public static function renderDetailButton(Email $mail): string
    {
        return Html::a('<i class="fa fa-search-plus"></i> Details', '#', [
            'class' => 'chat__details',
            'data-id' => $mail->e_id,
            'data-subject' => '<b>Subject: </b>' . Html::encode($mail->e_email_subject),
            'data-from' => $mail->isInbox()
                ? '<b>Email from:</b> (' . Html::encode($mail->e_email_from_name) . ' &lt;' . Html::encode($mail->e_email_from) . '&gt;)'
                : '<b>Email from:</b> ' . ($mail->eCreatedUser ? Html::encode($mail->eCreatedUser->username) : '-') . ', (' . Html::encode($mail->e_email_from_name) . ' &lt;' . Html::encode($mail->e_email_from) . '&gt;)',
            'data-to' => '<b>Email To:</b> ' . Html::encode($mail->e_email_to_name) . ' &lt;' . Html::encode($mail->e_email_to) . '&gt;',
            'data-date' => '<b>Date:</b> ' . Yii::$app->formatter->asDatetime(strtotime($mail->e_created_dt)),
            'data-files' => self::renderFilesData($mail->e_email_data)
        ]);
    }

    private static function renderFilesData(?string $data): string
    {
        if (!$data) {
            return '';
        }

        $files = @json_decode($data, true);

        if (!isset($files['files']) || !is_array($files['files'])) {
            return '';
        }

        return FileStorageEmailSentListWidget::create($files['files']);
    }
}
