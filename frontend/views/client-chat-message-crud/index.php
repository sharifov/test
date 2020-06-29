<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientChatMessage\entity\search\ClientChatMessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Messages';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-message-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Chat Message', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'ccm_id',
            'ccm_rid',
            'ccm_client_id',
            'ccm_user_id:userName',
            'ccm_sent_dt:byUserDateTime',
            'ccm_has_attachment',
            //'ccm_body',
            [
                'attribute' => 'message',
                'value' => function(\sales\model\clientChatMessage\entity\ClientChatMessage $model) {
                    if (is_null($model->ccm_body) || is_null($model->ccm_body['msg'])) {
                        return "";
                    }
                    return $model->ccm_body['msg'];
                },
                'format' => 'raw',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
