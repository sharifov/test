<?php

use common\components\grid\UserColumn;
use src\model\userData\entity\UserDataKey;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel src\model\userData\entity\search\UserDataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Data';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-data-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create User Data', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(['id' => 'pjax-user-data']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'ud_user_id',
                'relation' => 'user',
            ],
            [
                'attribute' => 'ud_key',
                'format' => 'userDataKey',
                'filter' => UserDataKey::getList(),
            ],
            'ud_value',
            [
                'class' => \common\components\grid\DateTimeColumn::class,
                'attribute' => 'ud_updated_dt',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
