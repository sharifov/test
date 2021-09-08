<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\leadRedial\entity\CallRedialUserAccess */

$this->title = 'Create Call Redial User Access';
$this->params['breadcrumbs'][] = ['label' => 'Call Redial User Accesses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-redial-user-access-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
