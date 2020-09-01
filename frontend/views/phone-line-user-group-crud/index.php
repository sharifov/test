<?php

use frontend\extensions\DatePicker;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\phoneLine\phoneLineUserGroup\entity\search\PhoneLineUserGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Phone Line User Groups';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="phone-line-user-group-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Phone Line User Group', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'plug_line_id',
            'plug_ug_id',
			[
				'attribute' => 'plug_created_dt',
				'format' => 'byUserDateTime',
				'filter' => DatePicker::widget([
					'model' => $searchModel,
					'attribute' => 'plug_created_dt',
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
				'attribute' => 'plug_updated_dt',
				'format' => 'byUserDateTime',
				'filter' => DatePicker::widget([
					'model' => $searchModel,
					'attribute' => 'plug_updated_dt',
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
