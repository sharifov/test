<?php

namespace modules\offer\src\helpers\formatters;

use modules\offer\src\entities\offer\Offer;
use modules\offer\src\entities\offerSendLog\OfferSendLogType;
use yii\bootstrap4\Html;

class OfferFormatter
{
    public static function asOffer(Offer $offer): string
    {
        return Html::a(
            'offer: ' . $offer->of_id,
            ['/offer/offer-crud/view', 'id' => $offer->of_id],
            ['target' => '_blank', 'data-pjax' => 0]
        );
    }

    public static function asSentView(Offer $offer): string
    {
        if ($offer->isViewed()) {
            $log = $offer->lastViewLog;
            return Html::tag('span', 'Viewed', [
                'class' => 'badge badge-success',
                'title' => \Yii::$app->formatter->asDatetime(strtotime($log->ofvwl_created_dt)),
            ]);
        }
        if ($offer->isSent()) {
            $log = $offer->lastSendLog;
            return Html::tag('span', 'Sent', [
                'class' => 'badge badge-warning',
                'title' =>
                    OfferSendLogType::getName($log->ofsndl_type_id) . ', '
                    . ($log->ofsndl_created_user_id ? $log->createdUser->username . ', ' : '')
                    . \Yii::$app->formatter->asDatetime(strtotime($log->ofsndl_created_dt)),
            ]);
        }
        return '';
    }
}
