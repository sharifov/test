<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\requestControl\models\UserSiteActivity */

$this->title = 'Update User Site Activity: ' . $model->usa_id;
$this->params['breadcrumbs'][] = ['label' => 'User Site Activities', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->usa_id, 'url' => ['view', 'id' => $model->usa_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-site-activity-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
