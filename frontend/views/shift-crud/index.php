<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\shiftSchedule\src\entities\shift\Shift;
use modules\shiftSchedule\src\entities\shiftCategory\ShiftCategoryQuery;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel \modules\shiftSchedule\src\entities\shift\search\SearchShift */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Shifts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shift-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Shift', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-shift', 'scrollTo' => 0]); ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['attribute' => 'sh_id', 'options' => [
                'style' => 'width: 80px'
            ]],
            //'sh_name',
            [
                'attribute' => 'sh_name',
                'value' => static function (Shift $model) {
                    return $model->getColorLabel() . ' ' . Html::encode($model->sh_name);
                },
                'format' => 'raw'
            ],
            'sh_title',
            [
                'attribute' => 'sh_category_id',
                'value' => static function (Shift $model) {
                    return $model->category ? Html::encode($model->category->sc_name ?? '') : null;
                },
                'filter' => ShiftCategoryQuery::getList()
            ],


            [
                'label' => 'Rules',
                'value' => static function (Shift $model) {
                    return $model->shiftScheduleRules ? Html::a(
                        count($model->shiftScheduleRules),
                        ['shift-schedule-rule-crud/index', 'SearchShiftScheduleRule[ssr_shift_id]' => $model->sh_id],
                        ['data-pjax' => 0, 'target' => '_blank']
                    )
                        : '-';
                },
                'format' => 'raw'
            ],

            [
                'label' => 'Users',
                'value' => static function (Shift $model) {
                    return $model->userShiftAssigns ? Html::a(
                        count($model->userShiftAssigns),
                        ['shift/user-shift-assign/index', 'UserShiftAssignListSearch[shiftId]' => $model->sh_id],
                        ['data-pjax' => 0, 'target' => '_blank']
                    )
                        : '-';
                },
                'format' => 'raw'
            ],

            ['class' => \common\components\grid\BooleanColumn::class, 'attribute' => 'sh_enabled'],
            'sh_color',
            'sh_sort_order',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'sh_created_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'sh_updated_dt',
                'format' => 'byUserDateTime'
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'sh_created_user_id',
                'relation' => 'createdUser',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'sh_updated_user_id',
                'relation' => 'updatedUser',
                'format' => 'username',
                'placeholder' => 'Select User'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
