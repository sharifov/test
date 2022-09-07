<?php

use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use modules\taskList\src\entities\TargetObject;
use modules\taskList\src\entities\userTask\UserTask;
use modules\taskList\src\entities\userTask\UserTaskHelper;
use src\helpers\NumberHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\taskList\src\entities\userTask\UserTaskSearch */
/* @var array $result */

$this->title = 'User Task Report';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
\frontend\assets\ChartJsAsset::register($this);
?>

<?php
    $allUserTaskCnt = $result['allUserTaskCnt'] ?? 0;
    $processingCnt = $result['processingCnt'] ?? 0;
    $completeCnt = $result['completeCnt'] ?? 0;
    $cancelCnt = $result['cancelCnt'] ?? 0;

    $processingPercent = NumberHelper::getPercent($processingCnt, $allUserTaskCnt);
    $completePercent = NumberHelper::getPercent($completeCnt, $allUserTaskCnt);
    $cancelPercent = NumberHelper::getPercent($cancelCnt, $allUserTaskCnt);
?>

<div class="user-task-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin([
        'id' => 'pjax-user-task-report',
        'timeout' => 9999,
        'enablePushState' => true,
        'enableReplaceState' => false,
        'scrollTo' => 0,
    ]); ?>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>&nbsp;</p>

    <div class="row">
        <div class="col-md-4 col-sm-4 ">
            <div class="x_panel tile" >
                <div class="x_title">
                    <h2>User Task</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <h4></h4>
                    <div class="widget_summary">
                        <div class="w_left w_25">
                            <span>All User Tasks</span>
                        </div>
                        <div class="w_center w_55">
                            <div class="progress">
                                <div class="progress-bar bg-green" role="progressbar" aria-valuenow="100"
                                     aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                                </div>
                            </div>
                        </div>
                        <div class="w_right w_20">
                            <span><?php echo $allUserTaskCnt ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="widget_summary">
                        <div class="w_left w_25">
                            <span>Processing</span>
                        </div>
                        <div class="w_center w_55">
                            <div class="progress">
                                <div class="progress-bar bg-green" role="progressbar" aria-valuenow="<?php echo $processingPercent ?>"
                                     aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $processingPercent ?>%;">
                                </div>
                            </div>
                        </div>
                        <div class="w_right w_20">
                            <span><?php echo $processingCnt ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="widget_summary">
                        <div class="w_left w_25">
                            <span>Complete</span>
                        </div>
                        <div class="w_center w_55">
                            <div class="progress">
                                <div class="progress-bar bg-green" role="progressbar" aria-valuenow="<?php echo $completePercent ?>"
                                     aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $completePercent ?>%;">
                                </div>
                            </div>
                        </div>
                        <div class="w_right w_20">
                            <span><?php echo $completeCnt ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="widget_summary">
                        <div class="w_left w_25">
                            <span>Cancel</span>
                        </div>
                        <div class="w_center w_55">
                            <div class="progress">
                                <div class="progress-bar bg-green" role="progressbar" aria-valuenow="<?php echo $cancelPercent ?>"
                                     aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $cancelPercent ?>%;">
                                </div>
                            </div>
                        </div>
                        <div class="w_right w_20">
                            <span><?php echo $cancelCnt ?></span>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php Pjax::end(); ?>

</div>
