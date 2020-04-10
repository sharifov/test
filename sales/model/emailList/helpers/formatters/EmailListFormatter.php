<?php

namespace sales\model\emailList\helpers\formatters;

use sales\model\emailList\entity\EmailList;
use yii\bootstrap4\Html;

class EmailListFormatter
{
    public static function asFormat(EmailList $email): string
    {
        if ($email->el_enabled) {
            return \Yii::$app->formatter->asEmail($email->el_email);
        }
        return  Html::tag('span', \Yii::$app->formatter->asEmail($email->el_email), ['class' => 'email-list']);
    }
}
