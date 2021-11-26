<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientDataKey\entity\ClientDataKey */

$this->title = $model->cdk_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Data Keys', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-data-key-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->cdk_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->cdk_id], [
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
                'cdk_id',
                'cdk_key',
                'cdk_name',
                'cdk_description',
                'cdk_enable:booleanByLabel',
                'cdk_is_system:booleanByLabel',
                'cdk_created_dt:byUserDateTime',
                'cdk_updated_dt:byUserDateTime',
                'cdk_created_user_id:username',
                'cdk_updated_user_id:username',
            ],
        ]) ?>

    </div>

</div>
