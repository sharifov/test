<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model src\model\appProjectKey\entity\AppProjectKey */

$this->title = 'Update App Project Key: ' . $model->apk_id;
$this->params['breadcrumbs'][] = ['label' => 'App Project Keys', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->apk_id, 'url' => ['view', 'id' => $model->apk_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="app-project-key-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
