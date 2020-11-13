<?php

use yii\grid\ActionColumn;
use sales\model\clientChatLastMessage\entity\ClientChatLastMessage;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;

/* @var yii\web\View $this */
/* @var sales\model\clientChatLastMessage\entity\ClientChatLastMessageSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Client Chat Last Messages';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-last-message-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Chat Last Message', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'cclm_cch_id',
            [
                'attribute' => 'cclm_type_id',
                'value' => static function (ClientChatLastMessage $model) {
                    return $model::getTypeName($model->cclm_type_id);
                },
                'filter' => ClientChatLastMessage::TYPE_LIST,
                'format' => 'raw',
            ],
            [
                'attribute' => 'cclm_message',
                'contentOptions' => [
                    'class' => 'truncate'
                ],
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cclm_dt',
                'format' => 'byUserDateTime'
            ],

            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php
$css = <<<CSS
    .truncate {
       max-width: 150px !important;
       overflow: hidden;
       white-space: nowrap;
       text-overflow: ellipsis;
    }    
    .truncate:hover {
       overflow: visible;
       white-space: normal;
       width: auto;
    }
CSS;
$this->registerCss($css);
?>
