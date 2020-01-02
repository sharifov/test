<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\CreditCard */

$this->title = 'Card: ' . $model->cc_id;
$this->params['breadcrumbs'][] = ['label' => 'Credit Cards', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="credit-card-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->cc_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->cc_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="col-md-6">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'cc_id',
            'cc_number',
            'cc_cvv',
            'cc_display_number',
            /*[
                'label' => 'Number UnSecure',
                'value' => static function(\common\models\CreditCard $model) {
                    return $model->initNumber;
                },
            ],
            [
                'label' => 'CVV UnSecure',
                'value' => static function(\common\models\CreditCard $model) {
                    return $model->initCvv;
                },
            ],*/
            'cc_holder_name',
            'cc_expiration_month',
            'cc_expiration_year',


        ],
    ]) ?>
    </div>
    <div class="col-md-6">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [


                //'cc_type_id',
                [
                    'attribute' => 'cc_type_id',
                    'value' => static function(\common\models\CreditCard $model) {
                        return $model->typeName;
                    },
                ],
                [
                    'attribute' => 'cc_status_id',
                    'value' => static function(\common\models\CreditCard $model) {
                        return $model->statusName;
                    },
                ],
                //'cc_status_id',
                'cc_is_expired:boolean',
                'cc_created_user_id:UserName',
                'cc_updated_user_id:UserName',
                'cc_created_dt:ByUserDateTime',
                'cc_updated_dt:ByUserDateTime',
            ],
        ]) ?>
    </div>

</div>
