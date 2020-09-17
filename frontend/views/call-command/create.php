<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\call\entity\callCommand\CallCommand */

$this->title = 'Create Call Command';
$this->params['breadcrumbs'][] = ['label' => 'Call Commands', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-command-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
