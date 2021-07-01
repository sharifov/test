<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\contactPhoneData\entity\ContactPhoneData */

$this->title = $model->cpd_cpl_id;
$this->params['breadcrumbs'][] = ['label' => 'Contact Phone Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="contact-phone-data-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'cpd_cpl_id' => $model->cpd_cpl_id, 'cpd_key' => $model->cpd_key], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'cpd_cpl_id' => $model->cpd_cpl_id, 'cpd_key' => $model->cpd_key], [
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
                'cpd_cpl_id',
                'cpd_key',
                'cpd_value',
                'cpd_created_dt:byUserDateTime',
                'cpd_updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
