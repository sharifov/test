<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\fileStorage\src\entity\fileOrder\FileOrder */

$this->params['breadcrumbs'][] = ['label' => 'File Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="file-Order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'fo_id' => $model->fo_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'fo_id' => $model->fo_id], [
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
                'fo_or_id',
                'fo_pq_id',
                'fo_category_id',
            ],
        ]) ?>

    </div>

</div>
