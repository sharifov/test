<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\leadRedial\entity\CallRedialUserAccess */

$this->title = $model->crua_lead_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Redial User Accesses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="call-redial-user-access-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'crua_lead_id' => $model->crua_lead_id, 'crua_user_id' => $model->crua_user_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'crua_lead_id' => $model->crua_lead_id, 'crua_user_id' => $model->crua_user_id], [
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
                'crua_lead_id',
                'crua_user_id:username',
                'crua_created_dt:byUserDatetime',
                'crua_updated_dt:byUserDatetime',
            ],
        ]) ?>

    </div>

</div>
