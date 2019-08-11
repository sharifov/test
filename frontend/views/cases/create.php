<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\entities\cases\Cases */

$this->title = 'Create Cases';
$this->params['breadcrumbs'][] = ['label' => 'Cases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cases-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
