<?php

use common\models\Employee;
use sales\helpers\user\GravatarHelper;
use sales\model\user\entity\userStats\UserStatsSearch;
use sales\model\userModelSetting\service\UserModelSettingDictionary;
use sales\model\userModelSetting\service\UserModelSettingHelper;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\web\View;

/* @var yii\web\View $this */
/* @var UserStatsSearch $searchModel */
/* @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'User Statistic';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-stats-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(['id' => 'pjax-user-stats', 'timeout' => 7000, 'enablePushState' => true]); ?>

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
            'label' => 'Avatar',
            'value' => static function ($model) {
                $result = '<div style="width: 60px;">';
                $result .= Html::img(GravatarHelper::getUrlByEmail($model['email']), ['alt' => 'avatar', 'class' => 'img-circle profile_img']);
                $result .= '</div>';
                return $result;
            },
            'format' => 'raw',
            'contentOptions' => [
                'style' => ['width' => '80px;']
            ],
        ],
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
                if ($idleState === 1) {
                    return '<span class="label label-warning">Idle</span>';
                }
                return '<span class="label label-success">Online</span>';
            },
            'format' => 'raw',
            'filter' => false,
            'enableSorting' => false,
        ],
        [
            'label' => 'Shift Hours',
            'format' => 'raw',
            'filter' => false,
            'enableSorting' => false,
            'value' => static function ($model) {
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
            },
        ],
        [
            'label' => 'Shift Time',
            'format' => 'raw',
            'filter' => false,
            'enableSorting' => false,
            'value' => static function ($model) {
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
            },
        ]
    ] ?>

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
    if ($searchModel->isFieldShow(UserModelSettingDictionary::FIELD_CLIENT_CHAT_ACTIVE)) {
        $row = UserModelSettingHelper::getGridDefaultColumn(UserModelSettingDictionary::FIELD_CLIENT_CHAT_ACTIVE);
        $row['value'] = static function ($model) {
            return (int) $model[UserModelSettingDictionary::FIELD_CLIENT_CHAT_ACTIVE];
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
