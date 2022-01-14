<?php

use frontend\helpers\JsonHelper;
use src\model\leadRequest\entity\LeadRequest;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\leadRequest\entity\LeadRequest */

$this->title = $model->lr_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lead-request-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->lr_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->lr_id], [
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
                'lr_id',
                'lr_type',
                'lr_job_id',
                [
                    'attribute' => 'lr_lead_id',
                    'value' => static function (LeadRequest $model) {
                        return Yii::$app->formatter->asLead($model->lead, 'fa-cubes');
                    },
                    'format' => 'raw',
                ],
                'lr_created_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

    <div class="col-md-8">
        <h2>Json data:</h2>
        <?php if ($model->lr_json_data) : ?>
            <pre>
            <?php \yii\helpers\VarDumper::dump(JsonHelper::decode($model->lr_json_data), 20, true) ?>
            </pre>
        <?php endif;?>
    </div>

</div>
