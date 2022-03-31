<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\requestControl\models\Rule */

$this->title = 'Request Control Rule:' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Request Control', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="request-control-rule-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
