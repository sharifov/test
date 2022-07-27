<?php

namespace frontend\helpers;

use common\models\Email;
use modules\fileStorage\src\widgets\FileStorageEmailSentListWidget;
use src\helpers\email\MaskEmailHelper;
use Yii;
use yii\helpers\Html;

/**
 * @deprecated
 * use widget instead CommunicationListItemWidget
 */
class EmailHelper
{
    public static function renderDetailButton(Email $mail): string
    {
        return Html::a('<i class="fa fa-search-plus"></i> Details', '#', [
            'class' => 'chat__details',
            'data-id' => $mail->e_id,
            'data-subject' => '<b>Subject: </b>' . Html::encode($mail->e_email_subject),
            'data-from' => $mail->isInbox()
                ? '<b>Email from:</b> (' . Html::encode($mail->e_email_from_name) . ' &lt;' . Html::encode(MaskEmailHelper::masking($mail->e_email_from)) . '&gt;)'
                : '<b>Email from:</b> ' . ($mail->eCreatedUser ? Html::encode($mail->eCreatedUser->username) : '-') . ', (' . Html::encode($mail->e_email_from_name) . ' &lt;' . Html::encode($mail->e_email_from) . '&gt;)',
            'data-to' => $mail->isInbox()
                ? '<b>Email To:</b> ' . Html::encode($mail->e_email_to_name) . ' &lt;' . Html::encode($mail->e_email_to) . '&gt;'
                : '<b>Email To:</b> ' . Html::encode($mail->e_email_to_name) . ' &lt;' . Html::encode(MaskEmailHelper::masking($mail->e_email_to)) . '&gt;',
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

    public static function getIconForCommunicationBlock(Email $mail): string
    {
        if ($mail->statusIsDone()) {
            $statusTitle = 'DONE - ' . ($mail->e_status_done_dt ? Yii::$app->formatter->asDatetime(strtotime($mail->e_status_done_dt)) : Yii::$app->formatter->asDatetime(strtotime($mail->e_updated_dt)));
            return Html::tag('i', null, [
                'class' => 'chat__status chat__status--success fa fa-circle',
                'data-toggle' => 'tooltip',
                'title' => $statusTitle,
                'data-placement' => 'right',
                'data-original-title' => $statusTitle
            ]);
        }

        if ($mail->statusIsErrorGroup()) {
            $statusTitle = 'ERROR - ' . $mail->e_error_message;
            return Html::tag('i', null, [
                'class' => 'chat__status chat__status--error fa fa-circle',
                'data-toggle' => 'tooltip',
                'title' => $statusTitle,
                'data-placement' => 'right',
                'data-original-title' => $statusTitle
            ]);
        }

        if ($mail->statusIsReview()) {
            $statusTitle = 'Email on review';
            return Html::tag('i', null, [
                'class' => 'chat__status warning fas fa-exclamation-triangle',
                'data-toggle' => 'tooltip',
                'title' => $statusTitle,
                'data-placement' => 'right',
                'data-original-title' => $statusTitle
            ]);
        }

        $statusTitle = 'SENT - ComID: ' . $mail->e_communication_id;
        return Html::tag('i', null, [
            'class' => 'chat__status chat__status--sent fa fa-circle',
            'data-toggle' => 'tooltip',
            'title' => $statusTitle,
            'data-placement' => 'right',
            'data-original-title' => $statusTitle
        ]);
    }
}
