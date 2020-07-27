<?php

use common\components\grid\DateTimeColumn;
use sales\model\clientChatCase\entity\ClientChatCase;
use sales\model\clientChatCase\entity\search\ClientChatCaseSearch;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel ClientChatCaseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Cases';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-case-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Chat Case', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'cccs_chat_id',
                'format' => 'clientChat',
                'value' => static function (ClientChatCase $model) {
                    return $model->chat;
                }
            ],
            [
                'attribute' => 'cccs_case_id',
                'format' => 'Case',
                'value' => static function (ClientChatCase $model) {
                    return $model->case;
                }
            ],
            [
                'attribute' => 'cccs_created_dt',
                'class' => DateTimeColumn::class
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
