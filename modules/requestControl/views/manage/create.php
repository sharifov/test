<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\requestControl\models\Rule */

$this->title = 'Create Request Control Rule';
$this->params['breadcrumbs'][] = ['label' => 'Request Control', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="request-control-rule-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
