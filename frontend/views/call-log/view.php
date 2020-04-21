<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLog\CallLog */

$this->title = $model->cl_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="call-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->cl_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->cl_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">
        <div class="col-md-4">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'cl_id',
                    'cl_group_id',
                    'cl_call_sid',
                    'cl_type_id:callLogType',
                    'cl_category_id:callLogCategory',
                    'cl_is_transfer:booleanByLabel',
                    'cl_duration',
                    'cl_phone_from',
                    'cl_phone_to',
                    'phoneList.pl_phone_number',
                    'cl_user_id:userName',
                    'cl_department_id:department',
                    'cl_project_id:projectName',
                    'cl_call_created_dt:byUserDateTime',
                    'cl_call_finished_dt:byUserDateTime',
                    'cl_status_id:callLogStatus',
                    'cl_client_id:client',
                    'cl_price',
                ],
            ]) ?>
        </div>
    </div>

</div>
