<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\call\entity\callCommand\PhoneLineCommand */

$this->title = 'Create Phone Line Command';
$this->params['breadcrumbs'][] = ['label' => 'Phone Line Commands', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-line-command-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
