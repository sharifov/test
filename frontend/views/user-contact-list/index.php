<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserContactListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Contact Lists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-contact-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Contact List', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'ucl_user_id:userName',
            'ucl_client_id:client',
            'ucl_title',
            'ucl_description:ntext',
            [
                'attribute' => 'ucl_favorite',
                'value' => function(\common\models\UserContactList $model) {
                    $out = '<span class="not-set">(not set)</span>';
                    if (isset($model->ucl_favorite)) {
                        $out = $model->ucl_favorite ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>';
                    }
                    return $out;
                },
                'format' => 'raw',
                'filter' => [1 => 'Yes', 0 => 'No']
            ],
            'ucl_created_dt:byUserDateTime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
