<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmailSearch */
/* @var $modelEmailView common\models\Email */
/* @var $modelNewEmail common\models\Email */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Emails';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="">

    <?/*<div class="page-title">
        <div class="title_left">
            <h3>Email Inbox <small></small></h3>
        </div>

        <div class="title_right">
            <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search for...">
                    <span class="input-group-btn">
                      <button class="btn btn-default" type="button">Go!</button>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>*/ ?>

    <div class="row">


        <div id="preloader" class="overlay" style="display: none">
            <div class="preloader">
                <span class="fa fa-spinner fa-pulse fa-3x fa-fw"></span>
                <div class="preloader__text">Loading...</div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>My Inbox<small>User Mail</small></h2>
                    <?php /*<ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#">Settings 1</a>
                                </li>
                                <li><a href="#">Settings 2</a>
                                </li>
                            </ul>
                        </li>
                        <li><a class="close-link"><i class="fa fa-close"></i></a>
                        </li>
                    </ul>*/?>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">

                    <?php Pjax::begin(['id' => 'pjax-email']); ?>



                    <?/*php $form = Form::begin([
                        'action' => ['index'],
                        'method' => 'get',
                        'options' => [
                            'data-pjax' => 1
                        ],
                    ]);*/ ?>

                    <div class="row">
                        <div class="col-sm-3 mail_list_column">

                            <?= Html::beginForm(\yii\helpers\Url::current(['email_type_id' => null, 'email_project_id' => null, 'action' => null]), 'GET', ['data-pjax' => 1]) ?>
                                <div class="col-md-3">
                                    <?=Html::a('<i class="fa fa-envelope"></i> Create NEW', \yii\helpers\Url::current(['id' => null, 'reply_id' => null, 'edit_id' => null, 'action' => 'new']), ['class' => 'btn btn-sm btn-success'])?>
                                </div>
                                <div class="col-md-4">
                                    <?=Html::dropDownList('email_type_id', Yii::$app->request->get('email_type_id'), \common\models\Email::FILTER_TYPE_LIST, ['class' => 'form-control', 'onchange' => '$("#btn-submit-email").click();'])?>
                                    <?= Html::submitButton('Ok', ['id' => 'btn-submit-email', 'class' => 'btn btn-primary hidden']) ?>
                                </div>
                                <div class="col-md-5">
                                    <?=Html::dropDownList('email_project_id', Yii::$app->request->get('email_project_id'), \common\models\Project::getList(), ['prompt' => 'ALL', 'class' => 'form-control', 'onchange' => '$("#btn-submit-email").click();'])?>
                                </div>
                            <?= Html::endForm() ?>

                            <?= ListView::widget([
                                'dataProvider' => $dataProvider,

                                'options' => [
                                    'tag' => 'div',
                                    'class' => 'list-wrapper',
                                    'id' => 'list-wrapper',
                                ],
                                'layout' => "{summary}\n{pager}\n{items}\n{summary}",
                                'itemView' => function ($model, $key, $index, $widget) use ($modelEmailView, $dataProvider) {
                                    return $this->render('_list_item',['model' => $model, 'modelEmailView' => $modelEmailView, 'dataProvider' => $dataProvider]);
                                },

                                'itemOptions' => [
                                    //'class' => 'item',
                                    'tag' => false,
                                ],

                                /*'pager' => [
                                    'firstPageLabel' => 'first',
                                    'lastPageLabel' => 'last',
                                    'nextPageLabel' => 'next',
                                    'prevPageLabel' => 'previous',
                                    'maxButtonCount' => 3,
                                ],*/

                            ]) ?>



                        </div>
                        <!-- /MAIL LIST -->

                        <!-- CONTENT MAIL -->
                        <div class="col-sm-9 mail_view">
                            <?php if($modelEmailView): ?>
                                <?=$this->render('_view_mail', ['model' => $modelEmailView])?>
                            <? endif; ?>



                            <?php if(Yii::$app->request->get('action') === 'new' || Yii::$app->request->get('edit_id') || Yii::$app->request->get('reply_id')): ?>
                                <?=$this->render('_new_mail', ['model' => $modelNewEmail])?>
                            <? endif; ?>

                        </div>
                        <!-- /CONTENT MAIL -->
                    </div>


                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>


<?/*php Pjax::begin(['id' => 'pjax-view-mail']); ?>
<?php Pjax::end();*/ ?>


<?php
$js = <<<JS
$("#pjax-email").on("pjax:start", function() {
    $('#preloader').show(); //fadeOut('slow');
});
$("#pjax-email").on("pjax:end", function() {
    $('#preloader').hide(); //fadeIn('slow');
});
JS;

//$this->registerJs($js, \yii\web\View::POS_READY);
