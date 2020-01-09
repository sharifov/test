<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SettingCategory */

$this->title = 'Create Setting Category';
$this->params['breadcrumbs'][] = ['label' => 'Setting Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="setting-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
