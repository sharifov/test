<?php

use common\models\Conference;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Conference */

$this->title = $model->cf_id;
$this->params['breadcrumbs'][] = ['label' => 'Conferences', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="conference-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->cf_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->cf_id], [
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
            'cf_id',
            'cf_cr_id',
            'cf_sid',
            'cf_call_sid',
            'cf_friendly_name',
            [
                'attribute' => 'cf_status_id',
                'value' => static function (Conference $model) {
                    return $model->getStatusName();
                }
            ],
            'cf_options:ntext',
            'cf_start_dt:byUserDateTime',
            'cf_end_dt:byUserDateTime',
            'cf_created_dt:byUserDateTime',
            'cf_updated_dt:byUserDateTime',
            'createdUser:userName'
        ],
    ]) ?>

</div>
