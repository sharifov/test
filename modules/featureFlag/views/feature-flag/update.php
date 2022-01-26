<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\featureFlag\src\entities\FeatureFlag */

$this->title = 'Update Feature Flag: ' . $model->ff_id;
$this->params['breadcrumbs'][] = ['label' => 'Feature Flags', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ff_id, 'url' => ['view', 'ff_id' => $model->ff_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="feature-flag-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
