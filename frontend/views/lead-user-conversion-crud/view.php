<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\leadUserConversion\entity\LeadUserConversion */

$this->title = $model->luc_lead_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead User Conversions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lead-user-conversion-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'luc_lead_id' => $model->luc_lead_id, 'luc_user_id' => $model->luc_user_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'luc_lead_id' => $model->luc_lead_id, 'luc_user_id' => $model->luc_user_id], [
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
                'luc_lead_id',
                'luc_user_id:userName',
                'luc_description',
                'luc_created_user_id:userName',
                'luc_created_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
