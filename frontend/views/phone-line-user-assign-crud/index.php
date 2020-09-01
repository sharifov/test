<?php

use frontend\extensions\DatePicker;
use sales\widgets\UserSelect2Widget;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\phoneLine\phoneLineUserAssign\entity\search\PhoneLineUserAssignSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Phone Line User Assigns';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-line-user-assign-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Phone Line User Assign', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'plus_line_id',
            'plus_user_id',
            'plus_allow_in:BooleanByLabel',
            'plus_allow_out:BooleanByLabel',
            'plus_uvm_id',
            'plus_enabled:BooleanByLabel',
            'plus_updated_dt',
			[
				'attribute' => 'plus_created_user_id',
				'filter' => UserSelect2Widget::widget([
					'model' => $searchModel,
					'attribute' => 'plus_created_user_id'
				]),
				'format' => 'username',
				'options' => [
					'width' => '150px'
				]
			],
			[
				'attribute' => 'plus_updated_user_id',
				'filter' => UserSelect2Widget::widget([
					'model' => $searchModel,
					'attribute' => 'plus_updated_user_id'
				]),
				'format' => 'username',
				'options' => [
					'width' => '150px'
				]
			],
			[
				'attribute' => 'plus_created_dt',
				'format' => 'byUserDateTime',
				'filter' => DatePicker::widget([
					'model' => $searchModel,
					'attribute' => 'plus_created_dt',
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
				'attribute' => 'plus_updated_dt',
				'format' => 'byUserDateTime',
				'filter' => DatePicker::widget([
					'model' => $searchModel,
					'attribute' => 'plus_updated_dt',
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
