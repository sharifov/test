<?php

use src\model\leadPoorProcessing\entity\LeadPoorProcessing;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\leadPoorProcessing\entity\LeadPoorProcessing */

$this->title = $model->lpp_lead_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Poor Processing', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lead-poor-processing-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'lpp_lead_id' => $model->lpp_lead_id, 'lpp_lppd_id' => $model->lpp_lppd_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'lpp_lead_id' => $model->lpp_lead_id, 'lpp_lppd_id' => $model->lpp_lppd_id], [
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
                [
                    'attribute' => 'lpp_lead_id',
                    'value' => static function (LeadPoorProcessing $model) {
                        return Yii::$app->formatter->asLead($model->lppLead, 'fa-cubes');
                    },
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'lpp_lppd_id',
                    'value' => static function (LeadPoorProcessing $model) {
                        return '<i class="fa fa-key"></i> ' . $model->lppLppd->lppd_key;
                    },
                    'format' => 'raw',
                ],
                'lpp_expiration_dt:byUserDatetime',
            ],
        ]) ?>

    </div>

</div>
