<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\abac\src\entities\abacDoc\AbacDoc */

$this->title = 'Create Abac Doc';
$this->params['breadcrumbs'][] = ['label' => 'Abac Docs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="abac-doc-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
