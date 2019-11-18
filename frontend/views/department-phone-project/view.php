<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\DepartmentPhoneProject */

$this->title = $model->dppProject->name . ' - ' .$model->dpp_id;
$this->params['breadcrumbs'][] = ['label' => 'Department Phone Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="department-phone-project-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->dpp_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->dpp_id], [
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
            'dpp_id',
            [
                'attribute' => 'dpp_dep_id',
                'value' => function (\common\models\DepartmentPhoneProject $model) {
                    return $model->dppDep ? $model->dppDep->dep_name : '-';
                },
            ],

            [
                'attribute' => 'dpp_project_id',
                'value' => function (\common\models\DepartmentPhoneProject $model) {
                    return $model->dppProject ? $model->dppProject->name : '-';
                },
            ],
            'dpp_phone_number',
            'dpp_redial:boolean',
            //'dpp_source_id',
            [
                'attribute' => 'dpp_source_id',
                'value' => function (\common\models\DepartmentPhoneProject $model) {
                    return $model->dppSource ? $model->dppSource->name : '-';
                },
            ],

            [
                'label' => 'User Groups',
                'value' => function (\common\models\DepartmentPhoneProject $model) {
                    $userGroupList = [];
                    if ($model->dugUgs) {
                        foreach ($model->dugUgs as $userGroup) {
                            $userGroupList[] =  '<span class="label label-info">' . Html::encode($userGroup->ug_name) . '</span>';
                        }
                    }
                    return $userGroupList ? implode(' ', $userGroupList) : '-';
                },
                'format' => 'raw',
            ],

            //'dpp_params',
            'dpp_ivr_enable:boolean',
            'dpp_enable:boolean',
            'dpp_description:text',
            [
                'attribute' => 'dpp_updated_user_id',
                'value' => function (\common\models\DepartmentPhoneProject $model) {
                    return $model->dpp_updated_user_id ? '<i class="fa fa-user"></i> ' .Html::encode($model->dppUpdatedUser->username) : $model->dpp_updated_user_id;
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'dep_updated_dt',
                'value' => function (\common\models\DepartmentPhoneProject $model) {
                    return $model->dpp_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->dpp_updated_dt)) : '-';
                },
                'format' => 'raw'
            ],
        ],
    ]) ?>

    <div class="row">
        <div class="col-md-12">
            <h2>Params:</h2>
        <?php
            \yii\helpers\VarDumper::dump(@json_decode($model->dpp_params, true), 10, true);
        ?>
        </div>
    </div>

</div>
