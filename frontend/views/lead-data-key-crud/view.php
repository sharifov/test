<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\leadDataKey\entity\LeadDataKey */

$this->title = $model->ldk_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Data Keys', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lead-data-key-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?php if (!$model->ldk_is_system) : ?>
                <?= Html::a('Update', ['update', 'id' => $model->ldk_id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Delete', ['delete', 'id' => $model->ldk_id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],
                ]) ?>
            <?php endif ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'ldk_id',
                'ldk_key',
                'ldk_name',
                'ldk_enable:booleanByLabel',
                'ldk_is_system:booleanByLabel',
                'ldk_created_dt:byUserDateTime',
                'ldk_updated_dt:byUserDateTime',
                'ldk_created_user_id:username',
                'ldk_updated_user_id:username',
            ],
        ]) ?>

    </div>

</div>
