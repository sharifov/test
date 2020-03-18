<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\entities\cases\CaseCategory */

$this->title = 'Create Cases Category';
$this->params['breadcrumbs'][] = ['label' => 'Cases Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="case-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
