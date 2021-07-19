<?php

use sales\model\coupon\entity\couponUserAction\CouponUserAction;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\coupon\entity\couponUserAction\CouponUserAction */

$this->title = $model->cuu_id;
$this->params['breadcrumbs'][] = ['label' => 'Coupon User Actions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="coupon-user-action-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->cuu_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->cuu_id], [
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
                'cuu_id',
                'cuu_coupon_id:coupon',
                [
                    'attribute' => 'cuu_action_id',
                    'format' => 'raw',
                    'value' => static function (CouponUserAction $model) {
                        return CouponUserAction::getActionName($model->cuu_action_id);
                    },
                ],
                [
                    'attribute' => 'cuu_api_user_id',
                    'format' => 'raw',
                    'value' => static function (CouponUserAction $model) {
                        return $model->cuuApiUser->au_name ?? Yii::$app->formatter->nullDisplay;
                    },
                ],
                'cuu_user_id:userName',
                'cuu_description',
                'cuu_created_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
