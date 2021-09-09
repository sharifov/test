<?php

use sales\model\clientChat\ClientChatPlatform;
use sales\model\clientChatMessage\entity\ClientChatMessage;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\UserSelect2Column;

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

    <?php Pjax::begin(['scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'ccm_id',
            'ccm_rid',
            'ccm_cch_id',
            'ccm_client_id',
            //'ccm_user_id:userName',
            [
                'attribute' => 'ccm_user_id',
                'format' => 'userName',
                'filter' => \common\models\Employee::getList()
            ],
            [
                'class' => common\components\grid\DateTimeColumn::class,
                'attribute' => 'ccm_sent_dt',
                'format' => 'byUserDateTime',
            ],
            'ccm_has_attachment',
            [
                'attribute' => 'ccm_platform_id',
                'value' => static function (ClientChatMessage $model) {
                    return ClientChatPlatform::getNameWithIcon($model->ccm_platform_id);
                },
                'filter' => ClientChatPlatform::getListName(),
                'format' => 'raw'
            ],
            //'ccm_body',
            [
                'attribute' => 'message',
                'value' => function (ClientChatMessage $model) {
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
