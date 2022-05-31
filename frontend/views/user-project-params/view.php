<?php

use common\models\UserProjectParams;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserProjectParams */

$this->title = $model->uppUser->username . ' - ' . $model->uppProject->name;
$this->params['breadcrumbs'][] = ['label' => 'User Project Params', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-project-params-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'upp_user_id' => $model->upp_user_id, 'upp_project_id' => $model->upp_project_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'upp_user_id' => $model->upp_user_id, 'upp_project_id' => $model->upp_project_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="col-md-6">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'upp_user_id',
                'value' => function (\common\models\UserProjectParams $model) {
                    return $model->uppUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->uppUser->username) . '' : '-';
                },
                'format' => 'raw',
            ],

            'upp_project_id:projectName',

            [
                'attribute' => 'upp_dep_id',
                'value' => function (\common\models\UserProjectParams $model) {
                    return $model->uppDep ? '' . $model->uppDep->dep_name . '' : '-';
                },
            ],

            'upp_email:email',
            'emailList.el_email:email',
            'upp_tw_phone_number',
            'phoneList.pl_phone_number',
            'upp_allow_general_line:booleanByLabel',
            'upp_allow_transfer:booleanByLabel',
            'upp_vm_enabled:booleanByLabel',
            [
                'attribute' => 'upp_vm_user_status_id',
                'value' => static function (UserProjectParams $model) {
                    return UserProjectParams::VM_USER_STATUS_LIST[$model->upp_vm_user_status_id] ?? null;
                },
            ],
            [
                'attribute' => 'upp_vm_id',
                'value' => static function (UserProjectParams $model) {
                    return $model->upp_vm_id ? $model->voiceMail->uvm_name : null;
                },
            ],
            //'upp_tw_sip_id',
            [
                'attribute' => 'upp_updated_dt',
                'value' => function (\common\models\UserProjectParams $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->upp_updated_dt));
                },
                'format' => 'raw',
            ],

            [
                'label' => 'Updated User',
                'attribute' => 'uppUpdatedUser.username',
            ],
        ],
    ]) ?>
    </div>
</div>
