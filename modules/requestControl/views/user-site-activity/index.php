<?php

use src\auth\Auth;
use src\services\cleaner\form\DbCleanerParamsForm;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\StringHelper;
use common\components\grid\DateTimeColumn;
use modules\requestControl\models\UserSiteActivity;

/* @var $this yii\web\View */
/* @var $searchModel modules\requestControl\models\search\UserSiteActivitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var DbCleanerParamsForm $modelCleaner */

$this->title = 'User Site Activities';
$this->params['breadcrumbs'][] = $this->title;
$pjaxListId = 'pjax-site-activity';
?>
<div class="user-site-activity-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php if (Auth::can('global/clean/table')) : ?>
        <?php echo $this->render('@frontend/views/clean/_clean_table_form', [
            'modelCleaner' => $modelCleaner,
            'pjaxIdForReload' => $pjaxListId,
        ]); ?>
    <?php endif ?>

    <?php Pjax::begin(['id' => $pjaxListId, 'scrollTo' => 0]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'usa_id',
            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'usa_user_id',
                'relation' => 'usaUser',
                'placeholder' => 'Select User',
            ],
            [
                'attribute' => 'usa_request_url',
                'contentOptions' => ['style' => 'max-width: 300px; word-wrap: break-word;'],
                'format' => 'raw',
                'value' => static function (UserSiteActivity $model) {
                    // Limit for string length
                    $stringLength = 90;

                    /*
                     * List for rendering.
                     * - If string is longer than value in `$stringLength` - we cut it and add the button for showing details.
                     * - If not - string will shown as is.
                     */
                    $renderList = (mb_strlen($model->usa_request_url) > $stringLength)
                        ? [
                            Html::tag('span', StringHelper::truncate(Html::encode($model->usa_request_url), $stringLength, '...', null, false)),
                            Html::tag('div', $model->usa_request_url, ['id' => "detail_{$model->usa_id}", 'style' => 'display: none;']),
                            Html::tag('i', '', ['class' => 'fas fa-eye green showDetail', 'style' => 'cursor: pointer; padding-left: 5px;', 'data-idt' => $model->usa_id])
                        ]
                        : [Html::tag('span', Html::encode($model->usa_request_url))];

                    // Replacing of concatenation. Render list will be imploded into the string with delimiter (empty space in this case)
                    return Html::tag('div', implode('', $renderList));
                },
            ],
            'usa_page_url',
            'usa_ip',
            [
                'attribute' => 'usa_request_type',
                'value' => static function (UserSiteActivity $model) {
                    return  $model->getRequestTypeName();
                },
                'filter' => UserSiteActivity::REQUEST_TYPE_LIST
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'usa_created_dt'
            ],
            [
                'label' => 'Duration',
                'value' => static function (UserSiteActivity $model) {
                    return Yii::$app->formatter->asRelativeTime(strtotime($model->usa_created_dt));
                },
                'format' => 'raw'
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php
yii\bootstrap4\Modal::begin([
    'title' => 'UserSiteActivityDetail',
    'id' => 'modal',
    'size' => \yii\bootstrap4\Modal::SIZE_LARGE,
]);
yii\bootstrap4\Modal::end();

$jsCode = <<<JS
    $(document).on('click', '.showDetail', function(){
        let logId = $(this).data('idt');
        let detailEl = $('#detail_' + logId);
        let modalBodyEl = $('#modal .modal-body');
        
        modalBodyEl.html('<p style="word-wrap: break-word;">' + detailEl.html() + '</p>');
        $('#modal-label').html('Detail User Site Activity (' + logId + ')');       
        $('#modal').modal('show');
        return false;
    });
JS;

$this->registerJs($jsCode, \yii\web\View::POS_READY);

