<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ClientProject */

$this->title = 'Create Client Project';
$this->params['breadcrumbs'][] = ['label' => 'Client Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-project-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
