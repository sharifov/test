<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model sales\model\clientAccount\entity\ClientAccount */

$this->title = 'Create Client Account';
$this->params['breadcrumbs'][] = ['label' => 'Client Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-account-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
