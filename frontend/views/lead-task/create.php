<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LeadTask */

$this->title = 'Create Lead Task';
$this->params['breadcrumbs'][] = ['label' => 'Lead Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-task-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
