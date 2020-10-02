<?php

use sales\model\clientChat\entity\actionReason\ClientChatActionReason;
use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\clientChat\entity\actionReason\search\actionReasonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Action Reasons';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-action-reason-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Chat Action Reason', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'ccar_id',
            [
                'attribute' => 'ccar_action_id',
                'value' => static function (ClientChatActionReason $model) {
                    return ClientChatStatusLog::getActionLabel($model->ccar_action_id);
                },
                'format' => 'raw',
                'filter' => ClientChatStatusLog::getActionList()
            ],
            'ccar_key',
            'ccar_name',
            'ccar_enabled:booleanByLabel',
            'ccar_comment_required:booleanByLabel',
            'ccar_created_user_id:username',
            'ccar_updated_user_id:username',
            'ccar_created_dt:byUserDateTime',
            'ccar_updated_dt:byUserDateTime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
