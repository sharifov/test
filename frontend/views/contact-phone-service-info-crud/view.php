<?php

use src\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfo;
use yii\bootstrap4\Html;
use yii\helpers\VarDumper;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfo */

$this->title = $model->cpsi_cpl_id;
$this->params['breadcrumbs'][] = ['label' => 'Contact Phone Service Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="contact-phone-service-info-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">
        <p>
            <?= Html::a('Update', ['update', 'cpsi_cpl_id' => $model->cpsi_cpl_id, 'cpsi_service_id' => $model->cpsi_service_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'cpsi_cpl_id' => $model->cpsi_cpl_id, 'cpsi_service_id' => $model->cpsi_service_id], [
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
                'cpsi_cpl_id',
                [
                    'attribute' => 'cpsi_service_id',
                    'value' => static function (ContactPhoneServiceInfo $model) {
                        return  ContactPhoneServiceInfo::getServiceName($model->cpsi_service_id);
                    },
                    'format' => 'raw',
                ],
                'cpsi_created_dt:byUserDateTime',
                'cpsi_updated_dt:byUserDateTime',
            ],
        ]) ?>
    </div>

    <div class="col-md-6">
        <strong><?php echo $model->getAttributeLabel('cpsi_data_json') ?></strong><br />
        <pre><small><?php VarDumper::dump($model->cpsi_data_json, 20, true); ?></small></pre><br />
    </div>
</div>
