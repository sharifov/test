<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \modules\shiftSchedule\src\entities\shiftCategory\ShiftCategory */

$this->title = $model->sc_name;
$this->params['breadcrumbs'][] = ['label' => 'Shift Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="shift-category-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'sc_id' => $model->sc_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'sc_id' => $model->sc_id], [
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
            'sc_id',
            'sc_name',
            'sc_created_user_id:username',
            'sc_updated_user_id:username',
            'sc_created_dt:byUserDateTime',
            'sc_updated_dt:byUserDateTime',
        ],
    ]) ?>

</div>
