<?php

use common\components\i18n\Formatter;
use modules\order\src\entities\orderContact\OrderContact;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderContact\OrderContact */

$this->title = $model->fullName;
$this->params['breadcrumbs'][] = ['label' => 'Order Contacts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-contact-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->oc_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->oc_id], [
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
                'oc_id',
                [
                    'attribute' => 'oc_order_id',
                    'value' => static function (OrderContact $model) {
                        return (new Formatter())->asOrder($model->ocOrder);
                    },
                    'format' => 'raw'
                ],
                'oc_first_name',
                'oc_last_name',
                'oc_middle_name',
                'oc_email:email',
                'oc_phone_number',
                'oc_created_dt:byUserDateTime',
                'oc_updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
