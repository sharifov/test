<?php

namespace frontend\helpers;

use common\models\Email;
use Yii;
use yii\helpers\Html;

class EmailHelper
{
    public static function renderDetailButton(Email $mail): string
    {
        return Html::a('<i class="fa fa-search-plus"></i> Details', '#', [
            'class' => 'chat__details',
            'data-id' => $mail->e_id,
            'data-from' => $mail->isInbox()
                ? '<b>Email from:</b> (' . Html::encode($mail->e_email_from_name) . ' <<strong>' . Html::encode($mail->e_email_from) . '> </strong>)'
                : '<b>Email from:</b> ' . ($mail->eCreatedUser ? Html::encode($mail->eCreatedUser->username) : '-') . ', (' . Html::encode($mail->e_email_from_name) . ' <<strong>' . Html::encode($mail->e_email_from) . '</strong>>)',
            'data-to' => '<b>Email To:</b> ' . Html::encode($mail->e_email_to_name) . ' <<strong>' . Html::encode($mail->e_email_to) . '</strong>>',
            'data-date' => Yii::$app->formatter->asDatetime(strtotime($mail->e_created_dt)),
            'data-subject' => '<b>Subject: </b>' . Html::encode($mail->e_email_subject),
        ]);
    }
}
