<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Conference */

$this->title = 'Create Conference';
$this->params['breadcrumbs'][] = ['label' => 'Conferences', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="conference-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
