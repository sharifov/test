<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use src\model\clientChat\componentRule\entity\ClientChatComponentRule;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\clientChat\componentRule\entity\search\ClientChatComponentRuleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Client Chat Component Rules';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-chat-component-rule-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Client Chat Component Rule', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-client-chat-component-rule', 'scrollTo' => 0]); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            [
                'attribute' => 'cccr_component_event_id',
                'value' => static function (ClientChatComponentRule $model) {
                    return Html::a($model->componentEvent->getComponentEventName() . ' (' . $model->componentEvent->ccce_id . ')', \yii\helpers\Url::to(['/client-chat-component-event-crud/view', 'id' => $model->cccr_component_event_id]));
                },
                'format' => 'raw'
            ],
            'cccr_value',
            [
                'attribute' => 'cccr_runnable_component',
                'value' => static function (ClientChatComponentRule $model) {
                    return $model->getComponentName();
                }
            ],
            'cccr_sort_order',
            [
                'attribute' => 'cccr_enabled',
                'value' => static function (ClientChatComponentRule $model) {
                    return Yii::$app->formatter->asBooleanByLabel($model->cccr_enabled);
                },
                'filter' => [1 => 'Yes', 0 => 'No'],
                'format' => 'raw',
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'cccr_created_user_id',
                'relation' => 'createdUser',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'cccr_updated_user_id',
                'relation' => 'updatedUser',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cccr_created_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'cccr_updated_dt',
                'format' => 'byUserDateTime'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
