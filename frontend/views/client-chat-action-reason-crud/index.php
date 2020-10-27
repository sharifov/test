<?php

use common\components\grid\BooleanColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
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
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

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
            ['class' => BooleanColumn::class, 'attribute' => 'ccar_enabled'],
            ['class' => BooleanColumn::class, 'attribute' => 'ccar_comment_required'],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ccar_created_user_id',
                'relation' => 'ccarCreatedUser',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ccar_updated_user_id',
                'relation' => 'ccarUpdatedUser',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ccar_created_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ccar_updated_dt',
                'format' => 'byUserDateTime'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
