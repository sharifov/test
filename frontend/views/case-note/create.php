<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\CaseNote */

$this->title = 'Create Case Note';
$this->params['breadcrumbs'][] = ['label' => 'Case Notes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="case-note-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
