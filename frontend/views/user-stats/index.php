<?php

use common\models\Employee;
use modules\featureFlag\FFlag;
use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftScheduleQuery;
use src\helpers\user\GravatarHelper;
use src\model\user\entity\userStats\UserStatsSearch;
use src\model\userModelSetting\service\UserModelSettingDictionary;
use src\model\userModelSetting\service\UserModelSettingHelper;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\View;

/* @var yii\web\View $this */
/* @var UserStatsSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'User Statistic';
$this->params['breadcrumbs'][] = $this->title;
/** @fflag FFlag::FF_KEY_SWITCH_NEW_SHIFT_ENABLE, Switch new Shift Enable */
$canNewShift = \Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_SWITCH_NEW_SHIFT_ENABLE);
?>
<div class="user-stats-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(['id' => 'pjax-user-stats', 'timeout' => 7000, 'enablePushState' => true, 'scrollTo' => 0]); ?>

    <div class="x_panel">
        <div class="x_title">
            <h2><i class="fa fa-search"></i> Search</h2>
            <ul class="nav navbar-right panel_toolbox">
                <li>
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </li>
            </ul>
            <div class="clearfix"></div>
        </div>
        <div class="x_content">
            <?php
            echo $this->render('_search', [
                'model' => $searchModel,
            ]);
            ?>
        </div>
    </div>

    <?php $columns = [
        [
            'attribute' => 'id',
            'value' => static function ($model) {
                return Html::a(
                    $model['username'],
                    ['user/info', 'id' => $model['id']],
                    ['title' => 'User info', 'target' => '_blank']
                );
            },
            'format' => 'raw',
            'enableSorting' => false,
            'filter' => false,
        ],
        [
            'attribute' => 'uo_idle_state',
            'value' => static function ($model) {
                $idleState = $model['uo_idle_state'];
                if ($idleState === null) {
                    return '<span class="label label-danger">Offline</span>';
                }
                if ($idleState === '1') {
                    return '<span class="label label-warning">Idle</span>';
                }
                return '<span class="label label-success">Online</span>';
            },
            'format' => 'raw',
            'filter' => false,
            'enableSorting' => false,
        ],
    ] ?>

    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_AVATAR)) {
        $rowField = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_AVATAR);
        $rowField['value'] = static function ($model) {
            $result = '<div style="width: 60px;">';
            $result .= Html::img(GravatarHelper::getUrlByEmail($model['email']), ['alt' => 'avatar', 'class' => 'img-circle profile_img']);
            $result .= '</div>';
            return $result;
        };
        $columns[] = $rowField;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_SHIFT_HOURS)) {
        $rowField = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_SHIFT_HOURS);
        $rowField['filter'] = false;
        $rowField['enableSorting'] = false;
        $rowField['value'] = static function ($model) use ($canNewShift) {
            if ($canNewShift) {
                $firstUserShiftSchedule = UserShiftScheduleQuery::getQueryForNextShiftsByUserId(
                    $model['id'],
                    (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))
                )
                    ->select(['uss_start_utc_dt', 'uss_end_utc_dt'])
                    ->limit(1)
                    ->asArray()
                    ->one();

                if (!$firstUserShiftSchedule) {
                    return 'Work start: ' . Yii::$app->formatter->nullDisplay;
                }
                return \Yii::$app->formatter->asDateTimeByUserTimezone(strtotime($firstUserShiftSchedule['uss_start_utc_dt']), ($model['up_timezone'] ?: 'UTC'), 'php:H:i')
                    . ' - ' .
                    \Yii::$app->formatter->asDateTimeByUserTimezone(strtotime($firstUserShiftSchedule['uss_end_utc_dt']), ($model['up_timezone'] ?: 'UTC'), 'php:H:i');
            } else {
                if (empty($model['up_work_start_tm'])) {
                    return 'Work start: ' . Yii::$app->formatter->nullDisplay;
                }
                if (empty($model['up_work_minutes'])) {
                    return 'Work minutes: ' . Yii::$app->formatter->nullDisplay;
                }

                $startDateTime = new DateTime($model['up_work_start_tm']);
                $startTime = $startDateTime->format('H:i');
                $endDateTime = $startDateTime->modify('+' . $model['up_work_minutes'] . ' minutes');
                $endTime = $endDateTime->format('H:i');
                return $startTime . ' - ' . $endTime;
            }
        };
        $columns[] = $rowField;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_SHIFT_TIME)) {
        $rowField = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_SHIFT_TIME);
        $rowField['filter'] = false;
        $rowField['enableSorting'] = false;
        $rowField['value'] = static function ($model) {
            if ((!$user = Employee::findOne($model['id'])) || !$user->checkShiftTime()) {
                return '';
            }
            try {
                $startDateTime = new DateTime($user->shiftTime->startUtcDt);
                $nowDateTime = new DateTime('now');
                $diffTime = $nowDateTime->diff($startDateTime);
                return $diffTime->format('%H:%i');
            } catch (\Throwable $throwable) {
                return '';
            }
        };
        $columns[] = $rowField;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_NICKNAME)) {
        $rowField = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_NICKNAME);
        $rowField['value'] = static function ($model) {
            return Html::encode($model[UserModelSettingDictionary::FIELD_NICKNAME]);
        };
        $columns[] = $rowField;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_SALES_CONVERSION)) {
        $rowField = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_SALES_CONVERSION);
        $rowField['value'] = static function ($model) {
            $sumShare = $model[UserModelSettingDictionary::FIELD_SPLIT_SHARE];
            $qualifiedLeadsTakenCount = $model[UserModelSettingDictionary::FIELD_LEADS_QUALIFIED_COUNT];
            $result = $qualifiedLeadsTakenCount > 0 ? round(($sumShare * 100) / $qualifiedLeadsTakenCount, 2) : 0;

            return Html::encode($result);
        };
        $columns[] = $rowField;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_SALES_CONVERSION_CALL_PRIORITY)) {
        $rowField = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_SALES_CONVERSION_CALL_PRIORITY);
        $rowField['value'] = static function ($model) {
            return Html::encode($model[UserModelSettingDictionary::FIELD_SALES_CONVERSION_CALL_PRIORITY]);
        };
        $columns[] = $rowField;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_CALL_PRIORITY_CURRENT)) {
        $rowField = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_CALL_PRIORITY_CURRENT);
        $rowField['value'] = static function ($model) {
            return Html::encode($model[UserModelSettingDictionary::FIELD_CALL_PRIORITY_CURRENT]);
        };
        $columns[] = $rowField;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_SUM_GROSS_PROFIT)) {
        $rowField = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_SUM_GROSS_PROFIT);
        $rowField['value'] = static function ($model) {
            return Html::encode($model[UserModelSettingDictionary::FIELD_SUM_GROSS_PROFIT]);
        };
        $columns[] = $rowField;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_GROSS_PROFIT_CALL_PRIORITY)) {
        $rowField = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_GROSS_PROFIT_CALL_PRIORITY);
        $rowField['value'] = static function ($model) {
            return Html::encode($model[UserModelSettingDictionary::FIELD_GROSS_PROFIT_CALL_PRIORITY]);
        };
        $columns[] = $rowField;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_LEADS_QUALIFIED_COUNT)) {
        $rowField = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_LEADS_QUALIFIED_COUNT);
        $rowField['value'] = static function ($model) {
            return Html::encode($model[UserModelSettingDictionary::FIELD_LEADS_QUALIFIED_COUNT]);
        };
        $columns[] = $rowField;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_LEADS_SOLD_COUNT)) {
        $rowField = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_LEADS_SOLD_COUNT);
        $rowField['value'] = function ($model) {
            return Html::encode($model[UserModelSettingDictionary::FIELD_LEADS_SOLD_COUNT]);
        };
        $columns[] = $rowField;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_SPLIT_SHARE)) {
        $rowField = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_SPLIT_SHARE);
        $rowField['value'] = function ($model) {
            return Html::encode(round($model[UserModelSettingDictionary::FIELD_SPLIT_SHARE], 2));
        };
        $columns[] = $rowField;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_LEADS_QUALIFIED_TAKEN_COUNT)) {
        $rowField = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_LEADS_QUALIFIED_TAKEN_COUNT);
        $rowField['value'] = function ($model) {
            return Html::encode($model[UserModelSettingDictionary::FIELD_LEADS_QUALIFIED_TAKEN_COUNT]);
        };
        $columns[] = $rowField;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_CLIENT_PHONE)) {
        $rowField = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_CLIENT_PHONE);
        $rowField['value'] = static function ($model) {
            $clientPhone = $model[UserModelSettingDictionary::FIELD_CLIENT_PHONE];
            return Html::encode((bool) $clientPhone ? 'ready' : 'busy');
        };
        $columns[] = $rowField;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_LEAD_CREATED)) {
        $rowField = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_LEAD_CREATED);
        $rowField['value'] = static function ($model) {
            return Html::encode($model[UserModelSettingDictionary::FIELD_LEAD_CREATED]);
        };
        $columns[] = $rowField;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_LEAD_SOLD)) {
        $row = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_LEAD_SOLD);
        $row['value'] = static function ($model) {
            return Html::encode($model[UserModelSettingDictionary::FIELD_LEAD_SOLD]);
        };
        $columns[] = $row;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_LEAD_TRASHED)) {
        $row = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_LEAD_TRASHED);
        $row['value'] = static function ($model) {
            return Html::encode($model[UserModelSettingDictionary::FIELD_LEAD_TRASHED]);
        };
        $columns[] = $row;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_LEAD_TAKEN)) {
        $row = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_LEAD_TAKEN);
        $row['value'] = static function ($model) {
            return (int) $model[UserModelSettingDictionary::FIELD_LEAD_TAKEN];
        };
        $columns[] = $row;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_LEAD_PROCESSING)) {
        $row = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_LEAD_PROCESSING);
        $row['value'] = static function ($model) {
            return Html::encode($model[UserModelSettingDictionary::FIELD_LEAD_PROCESSING]);
        };
        $columns[] = $row;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_CLIENT_CHAT_ACTIVE)) {
        $row = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_CLIENT_CHAT_ACTIVE);
        $row['value'] = static function ($model) {
            return (int) $model[UserModelSettingDictionary::FIELD_CLIENT_CHAT_ACTIVE];
        };
        $columns[] = $row;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_CLIENT_CHAT_PROGRESS)) {
        $row = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_CLIENT_CHAT_PROGRESS);
        $row['value'] = static function ($model) {
            return (int) $model[UserModelSettingDictionary::FIELD_CLIENT_CHAT_PROGRESS];
        };
        $columns[] = $row;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_CLIENT_CHAT_IDLE)) {
        $row = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_CLIENT_CHAT_IDLE);
        $row['value'] = static function ($model) {
            return (int) $model[UserModelSettingDictionary::FIELD_CLIENT_CHAT_IDLE];
        };
        $columns[] = $row;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_CLIENT_CHAT_CLOSED)) {
        $row = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_CLIENT_CHAT_CLOSED);
        $row['value'] = static function ($model) {
            return (int) $model[UserModelSettingDictionary::FIELD_CLIENT_CHAT_CLOSED];
        };
        $columns[] = $row;
    }
    ?>
    <?php
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_CLIENT_CHAT_TRANSFER)) {
        $row = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_CLIENT_CHAT_TRANSFER);
        $row['value'] = static function ($model) {
            return (int) $model[UserModelSettingDictionary::FIELD_CLIENT_CHAT_TRANSFER];
        };
        $columns[] = $row;
    }
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
    ]) ?>

    <?php Pjax::end(); ?>
</div>

<?php
$js = <<<JS
$(document).on('beforeSubmit', '#userStatsForm', function(event) {
    let btn = $(this).find('.js-user-stats-btn');
    
    btn.html('<span class="spinner-border spinner-border-sm"></span> Loading');        
    btn.prop("disabled", true)
});
JS;
$this->registerJs($js, View::POS_READY);

$css = <<<CSS
    #w1-filters { 
        display: none;
    }
CSS;
$this->registerCss($css);
?>
