<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileProductQuote\FileProductQuote */

$this->title = $model->fpq_fs_id;
$this->params['breadcrumbs'][] = ['label' => 'File Product Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="file-product-quote-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">
        <p>
            <?= Html::a('Update', ['update', 'fpq_fs_id' => $model->fpq_fs_id, 'fpq_pq_id' => $model->fpq_pq_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'fpq_fs_id' => $model->fpq_fs_id, 'fpq_pq_id' => $model->fpq_pq_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'fpq_fs_id',
                'fpq_pq_id',
                'fpq_created_dt:byUserDateTime',
            ],
        ]) ?>
    </div>
</div>
