<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\featureFlag\src\entities\FeatureFlag */

$this->title = 'Create Feature Flag';
$this->params['breadcrumbs'][] = ['label' => 'Feature Flags', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="feature-flag-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
