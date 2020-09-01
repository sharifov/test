<?php

use frontend\extensions\DatePicker;
use sales\model\phoneLine\phoneLine\entity\PhoneLine;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel \sales\model\phoneLine\phoneLine\entity\search\PhoneLineSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Phone Lines';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-line-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Phone Line', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'line_id',
            'line_name',
            [
                'attribute' => 'line_project_id',
                'filter' => \sales\widgets\ProjectSelect2Widget::widget([
                    'model' => $searchModel,
                    'attribute' => 'line_project_id'
                ]),
                'format' =>  'projectName',
				'options' => [
					'width' => '150px'
				]
            ],
            [
                'attribute' => 'line_dep_id',
                'filter' => \sales\widgets\DepartmentSelect2Widget::widget([
                    'model' => $searchModel,
                    'attribute' => 'line_dep_id'
                ]),
                'format' =>  'departmentName',
				'options' => [
					'width' => '150px'
				]
            ],
            'line_language_id',
			[
				'attribute' => 'line_personal_user_id',
				'filter' => \sales\widgets\UserSelect2Widget::widget([
					'model' => $searchModel,
					'attribute' => 'line_personal_user_id'
				]),
				'format' => 'username',
				'options' => [
					'width' => '150px'
				]
			],
            'line_uvm_id',
            'line_allow_in:BooleanByLabel',
            'line_allow_out:BooleanByLabel',
            'line_enabled:BooleanByLabel',
			[
                'attribute' => 'line_created_user_id',
                'filter' => \sales\widgets\UserSelect2Widget::widget([
                    'model' => $searchModel,
                    'attribute' => 'line_created_user_id'
                ]),
                'format' => 'username',
				'options' => [
					'width' => '150px'
				]
            ],
            [
                'attribute' => 'line_updated_user_id',
                'filter' => \sales\widgets\UserSelect2Widget::widget([
                    'model' => $searchModel,
                    'attribute' => 'line_updated_user_id'
                ]),
                'format' => 'username',
                'options' => [
                    'width' => '150px'
                ]
            ],
			[
				'attribute' => 'line_created_dt',
				'format' => 'byUserDateTime',
				'filter' => DatePicker::widget([
					'model' => $searchModel,
					'attribute' => 'line_created_dt',
					'clientOptions' => [
						'autoclose' => true,
						'format' => 'yyyy-mm-dd',
					],
					'options' => [
						'autocomplete' => 'off',
						'placeholder' =>'Choose Date',

					],
				]),
				'options' => [
					'width' => '150px'
				]
			],
            [
				'attribute' => 'line_updated_dt',
				'format' => 'byUserDateTime',
				'filter' => DatePicker::widget([
					'model' => $searchModel,
					'attribute' => 'line_updated_dt',
					'clientOptions' => [
						'autoclose' => true,
						'format' => 'yyyy-mm-dd',
					],
					'options' => [
						'autocomplete' => 'off',
						'placeholder' =>'Choose Date'
					],
				]),
				'options' => [
					'width' => '150px'
				]
			],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
