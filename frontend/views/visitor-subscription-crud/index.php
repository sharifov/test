<?php

use common\components\grid\DateTimeColumn;
use sales\model\visitorSubscription\entity\VisitorSubscription;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel sales\model\visitorSubscription\entity\search\VisitorSubscriptionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Visitor Subscriptions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="visitor-subscription-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Visitor Subscription', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-visitor-subscription', 'scrollTo' => 0]); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'vs_id',
            'vs_subscription_uid',
            [
                'attribute' => 'vs_type_id',
                'value' => static function (VisitorSubscription $model) {
                    return $model->getSubscriptionName();
                },
                'filter' => VisitorSubscription::getSubscriptionListName()
            ],
            [
                'attribute' => 'vs_enabled',
                'value' => static function (VisitorSubscription $model) {
                    return Yii::$app->formatter->asBooleanByLabel($model->vs_enabled);
                },
                'filter' => [1 => 'Yes', 0 => 'No'],
                'format' => 'raw',
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'vs_expired_date',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'vs_created_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'vs_updated_dt',
                'format' => 'byUserDateTime'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
