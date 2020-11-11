<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\phoneLinePhoneNumber\entity\PhoneLinePhoneNumber */

$this->title = $model->plpn_line_id;
$this->params['breadcrumbs'][] = ['label' => 'Phone Line Phone Numbers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="phone-line-phone-number-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'plpn_line_id' => $model->plpn_line_id, 'plpn_pl_id' => $model->plpn_pl_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'plpn_line_id' => $model->plpn_line_id, 'plpn_pl_id' => $model->plpn_pl_id], [
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
                'plpn_line_id',
                //'plpnPl:phoneList',
                'plpnPl.pl_phone_number',
                'plpn_default:BooleanByLabel',
                'plpn_enabled:BooleanByLabel',
                'plpn_settings_json:dumpJson',
                'plpn_created_user_id:username',
                'plpn_updated_user_id:username',
                'plpn_created_dt:byUserDateTime',
                'plpn_updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
