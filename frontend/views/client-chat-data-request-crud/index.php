<?php

use common\components\grid\DateTimeColumn;
use frontend\helpers\JsonHelper;
use sales\model\clientChatDataRequest\entity\ClientChatDataRequest;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientChatDataRequest\entity\search\ClientChatDataRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Data Requests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-data-request-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Chat Data Request', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'ccdr_id',
            'ccdr_chat_id',
            [
                'attribute' => 'ccdr_data_json',
                'value' => static function (ClientChatDataRequest $model) {
                    $content = '<p>' . StringHelper::truncate(JsonHelper::encode($model->ccdr_data_json), 216, '...', null, true) . '</p>';
                    $content .= Html::a(
                        '<i class="fas fa-eye"></i> details</a>',
                        null,
                        [
                            'class' => 'btn btn-sm btn-success',
                            'data-pjax' => 0,
                            'onclick' => '(function ( $event ) { $("#data_' . $model->ccdr_id . '").toggle(); })();',
                        ]
                    );
                    $content .= $model->ccdr_data_json ?
                        '<pre id="data_' . $model->ccdr_id . '" style="display: none;">' .
                        VarDumper::dumpAsString(JsonHelper::decode($model->ccdr_data_json), 10, true) . '</pre>' : '-';

                    return $content;
                },
                'format' => 'raw',
                'contentOptions' => [
                    'style' => ['max-width' => '800px', 'word-wrap' => 'break-word !important'],
                ],
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ccdr_created_dt',
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
