<?php

use frontend\helpers\JsonHelper;
use src\model\clientChatRequest\entity\ClientChatRequest;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\clientChatRequest\entity\search\ClientChatRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Requests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-request-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Client Chat Request', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php  echo $this->render('_pagination', ['model' => $searchModel]);?>

    <?= $searchModel->filterCount ? 'Find <b>' . $searchModel->filterCount . '</b> items' : null ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterUrl' => Url::to(['client-chat-request-crud/index']),
        'layout' => "{items}",
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'ccr_id',
                'enableSorting' => false,
            ],
            [
                'attribute' => 'ccr_event',
                'value' => static function (ClientChatRequest $model) {
                    return $model->getEventName();
                },
                'filter' => ClientChatRequest::getEventList()
            ],
            'ccr_rid',
            'ccr_visitor_id',
            [
                'attribute' => 'ccr_json_data',
                'value' => static function (ClientChatRequest $model) {
                    $content = '<p>' . StringHelper::truncate($model->ccr_json_data, 216, '...', null, true) . '</p>';
                    $content .= Html::a(
                        '<i class="fas fa-eye"></i> details</a>',
                        null,
                        [
                            'class' => 'btn btn-sm btn-success',
                            'data-pjax' => 0,
                            'onclick' => '(function ( $event ) { $("#data_' . $model->ccr_id . '").toggle(); })();',
                        ]
                    );
                    $content .= $model->ccr_json_data ?
                        '<pre id="data_' . $model->ccr_id . '" style="display: none;">' .
                            VarDumper::dumpAsString(JsonHelper::decode($model->ccr_json_data), 10, true) . '</pre>' : '-';

                    return $content;
                },
                'format' => 'raw',
                'contentOptions' => [
                    'style' => ['max-width' => '800px', 'word-wrap' => 'break-word !important'],
                ],
            ],
            [
                'class' => \common\components\grid\DateTimeColumn::class,
                'attribute' => 'ccr_created_dt',
                'format' => 'byUserDateTime'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php  echo $this->render('_pagination', ['model' => $searchModel]);?>

    <?php Pjax::end(); ?>

</div>

<?php
$css = <<<CSS
    .tooltip-inner {
         max-width: 800px;
         width: 800px; 
    }
CSS;
$this->registerCss($css);
?>

<?php $this->registerJs("
    $(function () {
        $('[data-toggle=\"tooltip\"]').tooltip({html:true});
    });
", $this::POS_END, 'tooltips'); ?>
