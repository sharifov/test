<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\userPersonalPhoneNumber\entity\UserPersonalPhoneNumber */

$this->title = $model->upn_id;
$this->params['breadcrumbs'][] = ['label' => 'User Personal Phone Numbers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-personal-phone-number-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->upn_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->upn_id], [
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
                'upn_id',
                'upn_user_id:username',
                [
                    'label' => 'Phone Number ID',
                    'attribute' => 'upn_phone_number',
                ],
                'upnPhoneNumber.pl_phone_number',
                'upn_title',
                'upn_approved:BooleanByLabel',
                'upn_enabled:BooleanByLabel',
                'upn_created_user_id:username',
                'upn_updated_user_id:username',
                'upn_created_dt:byUserDateTime',
                'upn_updated_dt:byUserDateTime',
            ],
        ]) ?>

    </div>

</div>
