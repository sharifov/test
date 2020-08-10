<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\monitor\UserMonitor */

$this->title = 'Create User Monitor';
$this->params['breadcrumbs'][] = ['label' => 'User Monitors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-monitor-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
