<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\entities\cases\CaseCategory */

$this->title = 'Create Case Category';
$this->params['breadcrumbs'][] = ['label' => 'Case Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="case-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
