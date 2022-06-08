<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DbDateSensitive */

$this->title = 'Create DB Date Sensitive';
$this->params['breadcrumbs'][] = ['label' => 'Db Date Sensitive', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="date-sensitive-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
