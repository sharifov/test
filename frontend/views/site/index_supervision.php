<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmployeeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = 'Dashboard - Supervision';
?>
<? /*<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>*/ ?>

<?php
$js = <<<JS
    google.charts.load('current', {packages: ['corechart', 'bar']});
JS;
//$this->registerJs($js, \yii\web\View::POS_READY);
$userId = Yii::$app->user->id;
?>

<div class="site-index">

    <h1>Supervision Dashboard</h1>
    <div class="row">


        <div class="col-md-4">
            <table class="table table-bordered">
                <tr>
                    <th>My Username:</th>
                    <td><?= Yii::$app->user->identity->username?> (<?=Yii::$app->user->id?>)</td>
                </tr>
                <tr>
                    <th>My Role:</th>
                    <td><?=implode(', ', Yii::$app->user->identity->roles)?></td>
                </tr>
            </table>

        </div>

        <div class="col-md-4">
            <table class="table table-bordered">
                <tr>
                    <th>My User Groups:</th>
                    <td>
                        <?php
                            $groupsValue = '';
                            if( $groupsModel =  Yii::$app->user->identity->ugsGroups) {
                                $groups = \yii\helpers\ArrayHelper::map($groupsModel, 'ug_id', 'ug_name');

                                $groupsValueArr = [];
                                foreach ($groups as $group) {
                                    $groupsValueArr[] = Html::tag('span', Html::encode($group), ['class' => 'label label-default']);
                                }
                                $groupsValue = implode(' ', $groupsValueArr);
                            }
                            echo $groupsValue;
                        ?>
                    </td>
                </tr>

            </table>

        </div>

        <div class="col-md-4">
            <table class="table table-bordered">
                <tr>
                    <th>Server Date Time</th>
                    <td><?= date('Y-m-d H:i:s')?></td>
                </tr>
                <tr>
                    <th>Formatted Local Date Time</th>
                    <td><?= Yii::$app->formatter->asDatetime(time())?></td>
                </tr>
            </table>

        </div>

    </div>

    <div class="panel panel-default">
        <div class="panel-heading">Agents Stats</div>
        <div class="panel-body">
            <?php Pjax::begin(); ?>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'rowOptions' => function (\common\models\Employee $model, $index, $widget, $grid) {
                    if ($model->deleted) {
                        return ['class' => 'danger'];
                    }
                },
                'columns' => [
                    [
                        'attribute' => 'id',
                        'contentOptions' => ['class' => 'text-center'],
                        'options' => ['style' => 'width:60px'],
                    ],
                    [
                        'attribute' => 'username',
                        'value' => function (\common\models\Employee $model) {
                            return Html::tag('i', '', ['class' => 'fa fa-user']).' '.Html::encode($model->username);
                        },
                        'format' => 'html',
                        //'contentOptions' => ['title' => 'text-center'],
                        'options' => ['style' => 'width:180px'],
                    ],

                    [
                        //'attribute' => 'username',
                        'label' => 'Role',
                        'value' => function (\common\models\Employee $model) {
                            $roles = $model->getRoles();
                            return $roles ? implode(', ', $roles) : '-';
                        },
                        'options' => ['style' => 'width:150px'],
                        //'format' => 'raw'
                    ],

                    /*'email:email',
                    [
                        'attribute' => 'status',
                        'filter' => [$searchModel::STATUS_ACTIVE => 'Active', $searchModel::STATUS_DELETED => 'Deleted'],
                        'value' => function (\common\models\Employee $model) {
                            return ($model->status === $model::STATUS_DELETED) ? '<span class="label label-danger">Deleted</span>' : '<span class="label label-success">Active</span>';
                        },
                        'format' => 'html'
                    ],*/

                    [
                        'label' => 'User Groups',
                        'attribute' => 'user_group_id',
                        'value' => function (\common\models\Employee $model) {

                            $groups = $model->getUserGroupList();
                            $groupsValueArr = [];

                            foreach ($groups as $group) {
                                $groupsValueArr[] = Html::tag('span', Html::tag('i', '', ['class' => 'fa fa-users']) . ' ' . Html::encode($group), ['class' => 'label label-default']);
                            }

                            $groupsValue = implode(' ', $groupsValueArr);

                            return $groupsValue;
                        },
                        'format' => 'html',
                        'filter' => Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) ? \common\models\UserGroup::getList() : Yii::$app->user->identity->getUserGroupList()
                    ],

                    [
                        'label' => 'Tasks Result for Period',
                        'value' => function(\common\models\Employee $model) use ($searchModel) {
                            return $model->getTaskStats($searchModel->datetime_start, $searchModel->datetime_end);
                        },
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'text-left'],
                        'filter' => \kartik\daterange\DateRangePicker::widget([
                            'model'=> $searchModel,
                            'attribute' => 'date_range',
                            //'name'=>'date_range',
                            'useWithAddon'=>true,
                            //'value'=>'2015-10-19 12:00 AM - 2015-11-03 01:00 PM',
                            'presetDropdown'=>true,
                            'hideInput'=>true,
                            'convertFormat'=>true,
                            'startAttribute' => 'datetime_start',
                            'endAttribute' => 'datetime_end',
                            //'startInputOptions' => ['value' => date('Y-m-d', strtotime('-5 days'))],
                            //'endInputOptions' => ['value' => '2017-07-20'],
                            'pluginOptions'=>[
                                'timePicker'=> false,
                                'timePickerIncrement'=>15,
                                'locale'=>['format'=>'Y-m-d']
                            ]
                        ])
                        //'options' => ['style' => 'width:200px'],

                    ],


                    /*[
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update}',
                        'visibleButtons' => [
                            'update' => function (\common\models\Employee $model, $key, $index) {
                                return (Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || !in_array('admin', array_keys($model->getRoles())));
                            },
                        ],

                    ],*/
                ]
            ])
            ?>

            <?php Pjax::end(); ?>
        </div>
    </div>



</div>