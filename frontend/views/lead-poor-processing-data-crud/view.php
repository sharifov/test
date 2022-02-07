<?php

use frontend\helpers\JsonHelper;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\leadPoorProcessingData\entity\LeadPoorProcessingData */

$this->title = $model->lppd_id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Poor Processing Datas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lead-poor-processing-data-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'lppd_id' => $model->lppd_id], ['class' => 'btn btn-primary']) ?>
    </p>

    <div class="row">
        <div class="col-md-6">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'lppd_id',
                    'lppd_enabled:booleanByLabel',
                    'lppd_key',
                    'lppd_name',
                    'lppd_description',
                    'lppd_minute',
                    'lppd_updated_dt:byUserDateTime',
                    'lppd_updated_user_id:userName',
                ],
            ]) ?>
        </div>
        <div class="col-md-6 bg-white">
            <h2>Params:</h2>
            <?=\yii\helpers\VarDumper::dumpAsString(JsonHelper::decode($model->lppd_params_json), 10, true) ?>
        </div>
    </div>
</div>
