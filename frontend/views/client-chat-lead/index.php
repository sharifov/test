<?php

use common\components\grid\DateTimeColumn;
use sales\model\clientChatLead\entity\ClientChatLead;
use sales\model\clientChatLead\entity\search\ClientChatLeadSearch;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel ClientChatLeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Leads';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-lead-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Chat Lead', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'ccl_chat_id',
                'format' => 'clientChat',
                'value' => static function (ClientChatLead $model) {
                    return $model->chat;
                }
            ],
            [
                'attribute' => 'ccl_lead_id',
                'format' => 'lead',
                'value' => static function (ClientChatLead $model) {
                    return $model->lead;
                }
            ],
            [
                'attribute' => 'ccl_created_dt',
                'class' => DateTimeColumn::class
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
