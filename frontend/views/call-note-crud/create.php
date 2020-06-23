<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\callNote\entity\CallNote */

$this->title = 'Create Call Note';
$this->params['breadcrumbs'][] = ['label' => 'Call Notes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-note-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
