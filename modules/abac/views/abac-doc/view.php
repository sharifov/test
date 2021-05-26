<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\abac\src\entities\abacDoc\AbacDoc */

$this->title = $model->ad_id;
$this->params['breadcrumbs'][] = ['label' => 'Abac Docs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="abac-doc-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->ad_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->ad_id], [
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
                'ad_id',
                'ad_file',
                'ad_line',
                'ad_subject',
                'ad_object',
                'ad_action',
                'ad_description',
                'ad_created_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
