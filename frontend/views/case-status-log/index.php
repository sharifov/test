<?php

use common\components\grid\DateTimeColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use src\entities\cases\CasesStatus;
use src\entities\cases\CaseStatusLog;
use common\models\Employee;
use yii\widgets\Pjax;
use src\auth\Auth;
use src\access\ListsAccess;

/* @var $this yii\web\View */
/* @var $searchModel src\entities\cases\CaseStatusLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Case Status History';
$this->params['breadcrumbs'][] = $this->title;

$userList = Employee::getList();
$user = Auth::user();
$lists = new ListsAccess($user->id);

?>
<div class="case-status-log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(['scrollTo' => 0]); ?>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            [
                'attribute' => 'csl_id',
                'options' => ['style' => 'width:100px'],
            ],
            [
                'attribute' => 'csl_from_status',
                'value' => static function (CaseStatusLog $model) {
                    return CasesStatus::getLabel($model->csl_from_status); //'<span class="label label-info">' . CasesStatus::getName($model->csl_from_status) . '</span></h5>';
                },
                'format' => 'raw',
                'filter' => CasesStatus::STATUS_LIST,
                //'options' => ['style' => 'width:180px'],
            ],
            [
                'attribute' => 'csl_to_status',
                'value' => static function (CaseStatusLog $model) {
                    return CasesStatus::getLabel($model->csl_to_status); //'<span class="label label-info">' . CasesStatus::getName($model->csl_to_status) . '</span></h5>';
                },
                'format' => 'raw',
                'filter' => CasesStatus::STATUS_LIST,
                //'options' => ['style' => 'width:180px'],
            ],
            [
                'attribute' => 'csl_case_id',
                'options' => ['style' => 'width:140px'],
            ],

            [
                'attribute' => 'project_id',
                'options' => ['style' => 'width:140px'],
                'label' => 'Project',
                'filter' => $lists->getProjects(),
            ],

            [
                'attribute' => 'department_id',
                'options' => ['style' => 'width:140px'],
                'label' => 'Department',
                'filter' => $lists->getDepartments(),
            ],

            [
                'label' => 'Status start date',
                'class' => DateTimeColumn::class,
                'attribute' => 'csl_start_dt'
            ],

            /*[
                'label' => 'Status start date',
                'attribute' => 'csl_start_dt',
                'value' => static function (CaseStatusLog $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->csl_start_dt));
                },
                'format' => 'raw',
                'options' => ['style' => 'width:180px'],
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'csl_start_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Choose Date'
                    ],
                ]),
            ],*/

            [
                'label' => 'Status end date',
                'class' => DateTimeColumn::class,
                'attribute' => 'csl_end_dt'
            ],

            /*[
                'label' => 'Status end date',
                'attribute' => 'csl_end_dt',
                'value' => static function (CaseStatusLog $model) {
                    return $model->csl_end_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->csl_end_dt)) : '';
                },
                'format' => 'raw',
                'options' => ['style' => 'width:180px'],
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'csl_end_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' => 'Choose Date'
                    ],
                ]),
            ],*/
            'csl_time_duration',
            [
                'attribute' => 'csl_owner_id',
                'value' => static function (CaseStatusLog $model) {
                    return $model->owner ? $model->owner->username : '';
                },
                'filter' => $userList
            ],
            [
                'attribute' => 'csl_created_user_id',
                'value' => static function (CaseStatusLog $model) {
                    return $model->createdUser ? $model->createdUser->username : '';
                },
                'filter' => $userList
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>
</div>
