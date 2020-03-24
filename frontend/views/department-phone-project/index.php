<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\DepartmentPhoneProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Department Phone Projects';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="department-phone-project-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Department Phone Project', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-hover'],
        'rowOptions' => function (\common\models\DepartmentPhoneProject $model) {
            if (!$model->dpp_enable) {
                return ['class' => 'danger'];
            }
            if (!$model->dpp_ivr_enable) {
                return ['class' => 'warning'];
            }
            return [];
        },
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'dpp_id',
            [
                'class' => \sales\yii\grid\project\ProjectColumn::class,
                'attribute' => 'dpp_project_id',
                'relation' => 'dppProject',
            ],
            'dpp_phone_number',
            [
                'class' => \sales\yii\grid\PhoneSelect2Column::class,
                'attribute' => 'dpp_phone_list_id',
                'relation' => 'phoneList',
            ],
            [
                'class' => \sales\yii\grid\BooleanColumn::class,
                'attribute' => 'dpp_redial',
            ],
            [
                'class' => \sales\yii\grid\department\DepartmentColumn::class,
                'attribute' => 'dpp_dep_id',
                'relation' => 'dppDep',
            ],
            [
                'label' => 'User Groups',
                'value' => static function (\common\models\DepartmentPhoneProject $model) {
                    $userGroupList = [];
                    if ($model->dugUgs) {
                        foreach ($model->dugUgs as $userGroup) {
                            $userGroupList[] =  '<span class="label label-info"><i class="fa fa-users"></i> ' . Html::encode($userGroup->ug_name) . '</span>';
                        }
                    }
                    return $userGroupList ? implode(' ', $userGroupList) : '-';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'dpp_source_id',
                'value' => static function (\common\models\DepartmentPhoneProject $model) {
                    return $model->dppSource ? $model->dppSource->name : '-';
                },
                'filter' => \common\models\Sources::getList(true)
            ],
            //'dpp_params',
            [
                'class' => \sales\yii\grid\BooleanColumn::class,
                'attribute' => 'dpp_ivr_enable',
            ],
            [
                'class' => \sales\yii\grid\BooleanColumn::class,
                'attribute' => 'dpp_enable',
            ],
            [
                'class' => \sales\yii\grid\BooleanColumn::class,
                'attribute' => 'dpp_default',
            ],
            [
                'class' => \sales\yii\grid\BooleanColumn::class,
                'attribute' => 'dpp_show_on_site',
            ],
            [
                'class' => \sales\yii\grid\UserColumn::class,
                'attribute' => 'dpp_updated_user_id',
                'relation' => 'dppUpdatedUser',
            ],
            [
                'class' => \sales\yii\grid\DateTimeColumn::class,
                'attribute' => 'dpp_updated_dt',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
