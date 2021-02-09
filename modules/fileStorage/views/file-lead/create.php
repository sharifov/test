<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileLead\FileLead */

$this->title = 'Create File Lead';
$this->params['breadcrumbs'][] = ['label' => 'File Leads', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-lead-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
