<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Lead */

$this->title = 'Create Lead';
$this->params['breadcrumbs'][] = ['label' => 'Leads', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
