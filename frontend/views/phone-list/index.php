<?php

use common\components\grid\BooleanColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use src\model\phoneList\entity\PhoneList;
use src\model\phoneList\entity\search\PhoneListSearch;
use common\components\grid\Select2Column;
use common\models\Project;

/* @var $this yii\web\View */
/* @var $searchModel src\model\phoneList\entity\search\PhoneListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Phone Lists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Phone List', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="fa fa-refresh"></i> Synchronization Phones from Communication', ['synchronization'], ['class' => 'btn btn-warning', 'data' => [
            'confirm' => 'Are you sure you want synchronization all projects from Communication Server?',
            'method' => 'post',
        ],]) ?>
    </p>

    <?php Pjax::begin(['timeout' => 5000, 'scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'pl_id',
            'pl_phone_number',
            [
                'label' => 'Phone Number Formatter',
                'attribute' => 'pl_phone_number',
                'contentOptions' => [
                    'style' => 'min-width: 140px;',
                ],
                'format' => 'raw',
                'value' =>  function (PhoneList $model) {
                    return \Yii::$app->formatter->asFormattedPhoneNumber($model->pl_phone_number);
                },
                'filter' => false,
            ],
            'pl_title',
            [
                'label' => 'Used for',
                'attribute' => 'used_for',
                'contentOptions' => [
                    'style' => 'min-width: 100px;',
                ],
                'format' => 'raw',
                'value' => function (PhoneList $model) {
                    $result = \Yii::$app->formatter->nullDisplay;
                    if (isset($model->departmentPhoneProject)) {
                        $result = 'General';
                        if (isset($model->userProjectParams)) {
                            $result .= ' and Personal';
                            return $result;
                        }
                    } elseif (isset($model->userProjectParams)) {
                        $result = 'Personal';
                    }
                    return $result;
                },
                'filter' => PhoneListSearch::getUsedForList(),
            ],
            [
                'label' => 'Projects',
                'attribute' => 'projects',
                'class' => Select2Column::class,
                'format' => 'raw',
                'value' => function (PhoneList $model) {
                    $projectIds = [];
                    if (isset($model->departmentPhoneProject)) {
                        $projectIds[] = $model->departmentPhoneProject->dpp_project_id;
                    }
                    if (isset($model->userProjectParams)) {
                        $projectIds[] = $model->userProjectParams->upp_project_id;
                    }
                    if (empty($projectIds)) {
                        return \Yii::$app->formatter->nullDisplay;
                    }
                    return \Yii::$app->formatter->asProjectNames($projectIds);
                },
                'id' => 'projects-filter',
                'data' => Project::getList(),
                'options' => ['width' => '200px', 'multiple' => true],
                'pluginOptions' => ['allowClear' => true, '']
            ],
            [
                'label' => 'Project Params users',
                'format' => 'raw',
                'attribute' => 'user_project_params_users',
                'value' => function (PhoneList $model) {
                    if (isset($model->userProjectParams) && isset($model->userProjectParams->upp_user_id)) {
                        return \Yii::$app->formatter->asUserNickname($model->userProjectParams->upp_user_id);
                    }
                    return \Yii::$app->formatter->nullDisplay;
                },
                'filter' => \src\widgets\UserSelect2Widget::widget([
                    'model' => $searchModel,
                    'attribute' => 'user_project_params_users',
                    'pluginOptions' => [
                        'width' => '100%',
                        'allowClear' => true
                    ],
                ]),

            ],
            ['class' => BooleanColumn::class, 'attribute' => 'pl_enabled'],
            ['class' => UserSelect2Column::class, 'attribute' => 'pl_created_user_id', 'relation' => 'createdUser'],
            ['class' => UserSelect2Column::class, 'attribute' => 'pl_updated_user_id', 'relation' => 'updatedUser'],
            ['class' => DateTimeColumn::class, 'attribute' => 'pl_created_dt'],
            ['class' => DateTimeColumn::class, 'attribute' => 'pl_updated_dt'],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
