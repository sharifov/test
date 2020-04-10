<?php

namespace common\components\grid\client;

use Yii;
use common\components\i18n\Formatter;
use common\models\Client;
use yii\grid\DataColumn;
use yii\helpers\ArrayHelper;
use yii\bootstrap4\Html;

/**
 * Class ClientPhonesColumn
 *
 * @property callable $client
 *
 * Ex.
    [
        'class' => \common\components\grid\client\ClientPhonesColumn::class,
        'client' => static function (LeadQcall $model) {
            return $model->lqcLead->client ?? null;
        },
    ],
 *
 */
class ClientPhonesColumn extends DataColumn
{
    public $label = 'Client / Phones';

    public $client;

    public function init(): void
    {
        parent::init();

        if (!$this->client) {
            throw new \InvalidArgumentException('relation must be set.');
        }

        if (!is_callable($this->client)) {
            throw new \InvalidArgumentException('relation must be callable.');
        }
    }

    protected function renderDataCellContent($model, $key, $index): string
    {
        if (!$client = call_user_func($this->client, $model, $key, $index, $this)) {
            return $this->grid->formatter->format(null, $this->format);
        }

        if (!$client instanceof Client) {
            throw new \InvalidArgumentException('client must be instanceof Client.');
        }

        $clientName = $client->first_name . ' ' . $client->last_name;

        if ($clientName === 'ClientName' || $clientName === 'Client Name') {
            $clientName = '- - - ';
        } elseif (Yii::$app->formatter instanceof Formatter) {
            $clientName = Yii::$app->formatter->asUserName($clientName);
        } else {
            $clientName = Html::tag('i', '', ['class' => 'fa fa-user']) . ' ' . Html::encode($clientName);
        }

        $str = $clientName . '<br>';

        if ($phones = $client->clientPhones) {
            $ico = Html::tag('i', '', ['class' => 'fa fa-phone']);
            $str .= $ico . ' ' . implode(' <br>' . $ico, ArrayHelper::map($phones, 'phone', 'phone'));
        }

        return $str;
    }
}
