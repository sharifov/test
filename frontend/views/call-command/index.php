<?php

use common\components\grid\UserSelect2Column;
use common\components\grid\DateTimeColumn;
use yii\grid\ActionColumn;
use sales\model\call\entity\callCommand\CallCommand;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var yii\web\View $this */
/* @var sales\model\call\entity\callCommand\search\CallCommandSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Call Commands';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-command-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a('Create Call Command', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]);?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'ccom_id',
            [
                'attribute' => 'ccom_type_id',
                'value' => static function (CallCommand $model) {
                    if ((int) $model->ccom_type_id === CallCommand::TYPE_COMMAND_LIST) {
                        $childrenCnt = (int) CallCommand::find()->where(['ccom_parent_id' => $model->ccom_id])->count();
                        return $model::getTypeName($model->ccom_type_id) . ' (' . $childrenCnt . ')';
                    }
                    return $model::getTypeName($model->ccom_type_id) ?: Yii::$app->formatter->nullDisplay;
                },
                'filter' => CallCommand::getTypeList(),
                'format' => 'raw',
                'enableSorting' => false,
            ],
            'ccom_name',
            'ccom_parent_id',
            [
                'attribute' => 'ccom_project_id',
                'value' => static function (CallCommand $model) {
                    return Yii::$app->formatter->asProjectName($model->ccomProject);
                },
                'filter' => \common\models\Project::getList(),
                'format' => 'raw',
            ],
            [
                'attribute' => 'ccom_lang_id',
                'value' => static function (CallCommand $model) {
                    return $model->ccom_lang_id ?: Yii::$app->formatter->nullDisplay;
                },
                'filter' => \common\models\Language::getLanguages(),
                'format' => 'raw',
            ],
            'ccom_sort_order',
            [
                'class' => UserSelect2Column::class,
                'attribute' => 'ccom_user_id',
                'relation' => 'ccomUser',
                 'options' => ['style' => 'width:140px']
            ],
            [
                'attribute' => 'ccom_created_user_id',
                'filter' => \sales\widgets\UserSelect2Widget::widget([
                    'model' => $searchModel,
                    'attribute' => 'ccom_created_user_id'
                ]),
                'format' => 'username',
                'options' => [
                    'width' => '150px'
                ]
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'ccom_created_dt'
            ],

            /*[
                'attribute' => 'ccom_created_dt',
                'value' => static function (CallCommand $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->ccom_created_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'ccom_created_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'clearBtn' => true,
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                    'clientEvents' => [
                        'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                    ],
                ]),
            ],*/
            ['class' => ActionColumn::class],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
