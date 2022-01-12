<?php

use src\model\visitorSubscription\entity\VisitorSubscription;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\visitorSubscription\entity\VisitorSubscription */

$this->title = $model->vs_id;
$this->params['breadcrumbs'][] = ['label' => 'Visitor Subscriptions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="visitor-subscription-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->vs_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->vs_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'vs_id',
                'vs_subscription_uid',
                [
                    'attribute' => 'vs_type_id',
                    'value' => static function (VisitorSubscription $model) {
                        return $model->getSubscriptionName();
                    },
                ],
                'vs_enabled:booleanByLabel',
                'vs_expired_date:byUserDate',
                'vs_created_dt:byUserDateTime',
                'vs_updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
