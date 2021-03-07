<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\AttractionPax */

$this->title = $model->atnp_id;
$this->params['breadcrumbs'][] = ['label' => 'Attraction Paxes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="attraction-pax-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->atnp_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->atnp_id], [
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
            'atnp_id',
            'atnp_atn_id',
            'atnp_type_id',
            'atnp_age',
            'atnp_first_name',
            'atnp_last_name',
            'atnp_dob',
        ],
    ]) ?>

</div>
