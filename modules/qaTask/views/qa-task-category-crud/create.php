<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTaskCategory\QaTaskCategory */

$this->title = 'Create Qa Task Category';
$this->params['breadcrumbs'][] = ['label' => 'Qa Task Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qa-task-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
