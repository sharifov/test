<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTask\QaTask */

$this->title = 'Update Qa Task: ' . $model->t_id;
$this->params['breadcrumbs'][] = ['label' => 'Qa Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->t_id, 'url' => ['view', 'id' => $model->t_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="qa-task-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
