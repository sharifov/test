<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productHolder\ProductHolder */

$this->title = $model->ph_id;
$this->params['breadcrumbs'][] = ['label' => 'Product Holders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="product-holder-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->ph_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->ph_id], [
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
                'ph_id',
                'ph_product_id',
                'ph_first_name',
                'ph_last_name',
                'ph_middle_name',
                'ph_email:email',
                'ph_phone_number',
                'ph_created_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
