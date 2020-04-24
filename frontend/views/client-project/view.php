<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ClientProject */

$this->title = $model->cp_client_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-project-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'cp_client_id' => $model->cp_client_id, 'cp_project_id' => $model->cp_project_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'cp_client_id' => $model->cp_client_id, 'cp_project_id' => $model->cp_project_id], [
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
            'cp_client_id',
            'cp_project_id',
            'cp_created_dt',
        ],
    ]) ?>

</div>
