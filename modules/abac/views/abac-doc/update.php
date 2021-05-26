<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\abac\src\entities\abacDoc\AbacDoc */

$this->title = 'Update Abac Doc: ' . $model->ad_id;
$this->params['breadcrumbs'][] = ['label' => 'Abac Docs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ad_id, 'url' => ['view', 'id' => $model->ad_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="abac-doc-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
