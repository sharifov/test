<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserGroupSet */

$this->title = $model->ugs_id;
$this->params['breadcrumbs'][] = ['label' => 'User Group Sets', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-group-set-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ugs_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->ugs_id], [
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
            'ugs_id',
            'ugs_name',
            'ugs_enabled:booleanByLabel',
            'ugs_created_dt:byUserDateTime',
            'ugs_updated_dt:byUserDateTime',
            'updatedUser:userName',
        ],
    ]) ?>

</div>
