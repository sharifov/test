<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\call\entity\callCommand\CallGatherSwitch */

$this->title = 'Create Call Gather Switch';
$this->params['breadcrumbs'][] = ['label' => 'Call Gather Switches', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-gather-switch-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
