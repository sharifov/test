<?php

use sales\model\clientAccount\entity\ClientAccount;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\clientAccount\entity\ClientAccount */

$this->title = $model->ca_id;
$this->params['breadcrumbs'][] = ['label' => 'Client Accounts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="client-account-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->ca_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->ca_id], [
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
                'ca_id',
                'ca_project_id:projectName',
                'ca_uuid',
                'ca_hid',
                'ca_username',
                'ca_first_name',
                'ca_middle_name',
                'ca_last_name',
                'ca_nationality_country_code',
                'ca_dob',
                [
                    'attribute' => 'ca_gender',
                    'value' => static function (ClientAccount $model) {
                        if ($model->ca_gender === ClientAccount::GENDER_MAN) {
                            return '<i class="fa fa-male"></i>';
                        }
                        if ($model->ca_gender === ClientAccount::GENDER_WOMAN) {
                            return '<i class="fa fa-female"></i>';
                        }
                        return '<i class="fa fa-transgender"></i>';
                    },
                    'format' => 'raw',
                ],
                'ca_phone',
                'ca_subscription:booleanByLabel',
                'ca_language_id',
                'ca_currency_code',
                'ca_timezone',
                'ca_created_ip',
                'ca_enabled:booleanByLabel',
                'ca_origin_created_dt:byUserDateTime',
                'ca_origin_updated_dt:byUserDateTime',
                'ca_created_dt:byUserDateTime',
                'ca_updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
