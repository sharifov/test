<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileLead\FileLead */

$this->title = 'Update File Lead';
$this->params['breadcrumbs'][] = ['label' => 'File Leads', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fld_fs_id, 'url' => ['view', 'fld_fs_id' => $model->fld_fs_id, 'fld_lead_id' => $model->fld_lead_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="file-lead-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
