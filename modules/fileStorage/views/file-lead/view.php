<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileLead\FileLead */

$this->params['breadcrumbs'][] = ['label' => 'File Leads', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="file-lead-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'fld_fs_id' => $model->fld_fs_id, 'fld_lead_id' => $model->fld_lead_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'fld_fs_id' => $model->fld_fs_id, 'fld_lead_id' => $model->fld_lead_id], [
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
                'fld_fs_id',
                'fld_lead_id',
            ],
        ]) ?>

    </div>

</div>
