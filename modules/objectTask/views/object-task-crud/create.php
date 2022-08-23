<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\objectTask\src\entities\ObjectTask */

$this->title = 'Create Object Task';
$this->params['breadcrumbs'][] = ['label' => 'Object Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="object-task-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
