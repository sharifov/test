<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileProductQuote\FileProductQuote */

$this->title = 'Update File Product Quote: ' . $model->fpq_fs_id;
$this->params['breadcrumbs'][] = ['label' => 'File Product Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fpq_fs_id, 'url' => ['view', 'fpq_fs_id' => $model->fpq_fs_id, 'fpq_pq_id' => $model->fpq_pq_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="file-product-quote-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
