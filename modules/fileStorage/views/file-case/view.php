<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileCase\FileCase */

$this->params['breadcrumbs'][] = ['label' => 'File Cases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="file-case-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'fc_fs_id' => $model->fc_fs_id, 'fc_case_id' => $model->fc_case_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'fc_fs_id' => $model->fc_fs_id, 'fc_case_id' => $model->fc_case_id], [
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
                'fc_fs_id',
                'fc_case_id',
            ],
        ]) ?>

    </div>

</div>
