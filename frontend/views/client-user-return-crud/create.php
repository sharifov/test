<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model src\model\clientUserReturn\entity\ClientUserReturn */

$this->title = 'Create Client User Return';
$this->params['breadcrumbs'][] = ['label' => 'Client User Returns', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-user-return-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
