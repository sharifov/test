<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ApiUser */

$this->title = 'Update Api User: ' . $model->au_id;
$this->params['breadcrumbs'][] = ['label' => 'Api Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->au_id, 'url' => ['view', 'id' => $model->au_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="api-user-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
