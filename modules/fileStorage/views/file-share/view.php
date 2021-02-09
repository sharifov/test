<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileShare\FileShare */

$this->title = $model->fsh_id;
$this->params['breadcrumbs'][] = ['label' => 'File Shares', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="file-share-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->fsh_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->fsh_id], [
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
                'fsh_id',
                'fsh_fs_id',
                'fsh_code',
                'fsh_expired_dt:byUserDatetime',
                'fsh_created_dt:byUserDatetime',
                'createdUser.nickname',
            ],
        ]) ?>

    </div>

</div>
