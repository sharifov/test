<?php

use common\models\CallUserAccess;
use sales\model\callLog\entity\callLogUserAccess\CallLogUserAccess;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLogUserAccess\CallLogUserAccess */

$this->title = $model->clua_cl_id;
$this->params['breadcrumbs'][] = ['label' => 'Call Log User Accesses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="call-log-user-access-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->clua_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->clua_id], [
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
                'clua_id',
                'clua_cl_id',
                'user:userName',
                [
                    'attribute' => 'clua_access_status_id',
                    'filter' => CallUserAccess::getStatusTypeList(),
                    'value' => static function (CallLogUserAccess $model) {
                        return CallUserAccess::getStatusTypeList()[$model->clua_access_status_id] ?? null;
                    },
                ],
                'clua_access_start_dt:byUserDatetime',
                'clua_access_finish_dt:byUserDatetime',
            ],
        ]) ?>

    </div>

</div>
