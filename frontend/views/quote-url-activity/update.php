<?php

use yii\helpers\Html;
use common\models\QuoteUrlActivity;

/* @var $this yii\web\View */
/* @var $model QuoteUrlActivity */

$this->title = 'Update Quote Url Activity: ' . $model->qua_id;
$this->params['breadcrumbs'][] = ['label' => 'Quote Url Activity', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->qua_id, 'url' => ['view', 'qua_id' => $model->qua_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="quote-url-activity-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
