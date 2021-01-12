<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileUser\FileUser */

$this->params['breadcrumbs'][] = ['label' => 'File Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="file-user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'fus_fs_id' => $model->fus_fs_id, 'fus_user_id' => $model->fus_user_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'fus_fs_id' => $model->fus_fs_id, 'fus_user_id' => $model->fus_user_id], [
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
                'fus_fs_id',
                'fus_user_id',
            ],
        ]) ?>

    </div>

</div>
