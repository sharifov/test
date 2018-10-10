<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataStats [] */
/* @var $dataSources [] */
/* @var $dataEmployee [] */
/* @var $dataEmployeeSold [] */

/* @var $searchModel common\models\search\LeadTaskSearch */
/* @var $dp1 yii\data\ActiveDataProvider */
/* @var $dp2 yii\data\ActiveDataProvider */
/* @var $dp3 yii\data\ActiveDataProvider */


$this->title = 'Dashboard - Supervision';
?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<?php
$js = <<<JS
    google.charts.load('current', {packages: ['corechart', 'bar']});
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
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





</div>