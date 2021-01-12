<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileClient\FileClient */

$this->params['breadcrumbs'][] = ['label' => 'File Clients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="file-client-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'fcl_fs_id' => $model->fcl_fs_id, 'fcl_client_id' => $model->fcl_client_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'fcl_fs_id' => $model->fcl_fs_id, 'fcl_client_id' => $model->fcl_client_id], [
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
                'fcl_fs_id',
                'fcl_client_id',
            ],
        ]) ?>

    </div>

</div>
