<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SettingCategory */

$this->title = 'Update Setting Category: ' . $model->sc_name;
$this->params['breadcrumbs'][] = ['label' => 'Setting Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->sc_id, 'url' => ['view', 'id' => $model->sc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="setting-category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
