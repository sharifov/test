<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\entities\cases\CaseCategory */

$this->title = 'Update Cases Category: ' . $model->cc_key;
$this->params['breadcrumbs'][] = ['label' => 'Cases Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cc_key, 'url' => ['view', 'id' => $model->cc_key]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="case-category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
