<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Department;
use sales\entities\cases\CasesCategory;

/* @var $this yii\web\View */
/* @var $searchModel sales\entities\cases\CasesCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cases Categories';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cases-category-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Cases Category', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => yii\grid\SerialColumn::class],

            'cc_key',
            'cc_name',
            [
                'attribute' => 'cc_dep_id',
                'format' => 'raw',
                'filter' => Department::getList(),
                'value' => static function (CasesCategory $model) {
                    return $model->dep ? $model->dep->dep_name : 'undefined';
                }
            ],
            [
                'attribute' => 'cc_system',
                'format' => 'boolean',
                'filter' => [1 => 'Yes', 0 => 'No']
            ],

            ['class' => yii\grid\ActionColumn::class],
        ],
    ]); ?>

</div>
