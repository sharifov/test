<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileCase\FileCase */

$this->title = 'Create File Case';
$this->params['breadcrumbs'][] = ['label' => 'File Cases', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="file-case-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
