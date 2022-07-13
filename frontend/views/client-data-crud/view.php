<?php

use src\model\clientData\entity\ClientData;
use src\model\clientDataKey\entity\ClientDataKey;
use src\model\clientDataKey\service\ClientDataKeyService;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model src\model\clientData\entity\ClientData */

$this->title = $model->cd_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Data', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-data-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->cd_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->cd_id], [
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
                'cd_id',
                'cd_client_id:client',
                [
                    'attribute' => 'cd_key_id',
                    'value' => static function (ClientData $model) {
                        if (!$key = ClientDataKeyService::getKeyByIdCache((int) $model->cd_key_id, null)) {
                            return Yii::$app->formatter->nullDisplay;
                        }
                        return Yii::$app->formatter->asLabel($key);
                    },
                    'format' => 'raw',
                ],
                'cd_field_value',
                'cd_field_value_ui',
                'cd_created_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
