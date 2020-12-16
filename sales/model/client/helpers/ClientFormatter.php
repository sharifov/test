<?php

namespace sales\model\client\helpers;

use common\models\Client;
use yii\helpers\Html;

class ClientFormatter
{
    public static function formatName(Client $client): string
    {
        return self::formatExclude($client) . Html::encode($client->first_name);
    }

    public static function formatFullName(Client $client): string
    {
        return self::formatExclude($client) . Html::encode($client->full_name ?: 'Client-' . $client->id);
    }

    public static function formatExclude(Client $client): string
    {
        if (!$client->isExcluded()) {
            return '';
        }

        return Html::tag('i', '', [
                'class' => 'fa fa-exclamation-triangle warning',
                'title' => 'Excluded Client'
            ]) . ' ';
    }
}
