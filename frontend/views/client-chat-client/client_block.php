<?php

use common\models\Client;
use yii\widgets\DetailView;

/** @var $client Client */

?>
<div class="row">
    <div class="col-md-6">
        <?= DetailView::widget([
            'model' => $client,
            'attributes' => [
                'id',
                [
                    'attribute' => 'first_name',
                    'value' => static function (Client $client) {
                        return \sales\model\client\helpers\ClientFormatter::formatName($client);
                    },
                    'format' => 'raw',
                ],
                'middle_name',
                'last_name',
            ],
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= DetailView::widget([
            'model' => $client,
            'attributes' => [
                'project:projectName',
                [
                    'attribute' => 'created',
                    'value' => static function (Client $client) {
                        return '<i class="fa fa-calendar"> </i> ' . Yii::$app->formatter->asDatetime(strtotime($client->created));
                    },
                    'format' => 'html',
                ],
                [
                    'attribute' => 'updated',
                    'value' => static function (Client $client) {
                        return '<i class="fa fa-calendar"> </i> ' . Yii::$app->formatter->asDatetime(strtotime($client->updated));
                    },
                    'format' => 'html',
                ],
            ],
        ]) ?>
    </div>
</div>
