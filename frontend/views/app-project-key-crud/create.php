<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\appProjectKey\entity\AppProjectKey */

$this->title = 'Create App Project Key';
$this->params['breadcrumbs'][] = ['label' => 'App Project Keys', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="app-project-key-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
