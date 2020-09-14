<?php

use frontend\extensions\DatePicker;
use sales\widgets\UserSelect2Widget;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel sales\model\phoneLine\userPersonalPhoneNumber\entity\search\UserPersonalPhoneNumberSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Personal Phone Numbers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-personal-phone-number-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Personal Phone Number', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'upn_id',
			[
				'attribute' => 'upn_user_id',
				'filter' => UserSelect2Widget::widget([
					'model' => $searchModel,
					'attribute' => 'upn_user_id'
				]),
				'format' => 'username',
				'options' => [
					'width' => '150px'
				]
			],
            'upn_phone_number',
            'upn_title',
            'upn_approved:BooleanByLabel',
            'upn_enabled:BooleanByLabel',
			[
				'attribute' => 'upn_created_user_id',
				'filter' => \sales\widgets\UserSelect2Widget::widget([
					'model' => $searchModel,
					'attribute' => 'upn_created_user_id'
				]),
				'format' => 'username',
				'options' => [
					'width' => '150px'
				]
			],
			[
				'attribute' => 'upn_updated_user_id',
				'filter' => \sales\widgets\UserSelect2Widget::widget([
					'model' => $searchModel,
					'attribute' => 'upn_updated_user_id'
				]),
				'format' => 'username',
				'options' => [
					'width' => '150px'
				]
			],
			[
				'attribute' => 'upn_created_dt',
				'format' => 'byUserDateTime',
				'filter' => DatePicker::widget([
					'model' => $searchModel,
					'attribute' => 'upn_created_dt',
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
				'attribute' => 'upn_updated_dt',
				'format' => 'byUserDateTime',
				'filter' => DatePicker::widget([
					'model' => $searchModel,
					'attribute' => 'upn_updated_dt',
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
