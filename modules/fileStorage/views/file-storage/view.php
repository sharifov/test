<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileStorage\FileStorage */

$this->title = $model->fs_id;
$this->params['breadcrumbs'][] = ['label' => 'File Storages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="file-storage-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Edit', ['edit', 'id' => $model->fs_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->fs_id], [
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
                'fs_id',
                'fs_uid',
                'fs_mime_type',
                'fs_name',
                'fs_title',
                'fs_path',
                'fs_size',
                'fs_md5_hash',
                'fs_private:booleanByLabel',
                'fs_expired_dt:byUserDatetime',
                'fs_created_dt:byUserDatetime',
            ],
        ]) ?>

    </div>

</div>
