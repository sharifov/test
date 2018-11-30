<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmailSearch */
/* @var $modelEmailView common\models\Email */
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

                    <?php Pjax::begin(); ?>
                    <div class="row">
                        <div class="col-sm-3 mail_list_column">
                            <button id="compose" class="btn btn-sm btn-success btn-block" type="button">COMPOSE</button>


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

                                    // or just do some echo
                                    // return $model->title . ' posted by ' . $model->author;
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

                                /*'itemView' => function ($model, $key, $index, $widget) {
                                    return Html::a(Html::encode($model->e_id), ['view', 'id' => $model->e_id]);
                                },*/
                            ]) ?>



                        </div>
                        <!-- /MAIL LIST -->

                        <!-- CONTENT MAIL -->
                        <div class="col-sm-9 mail_view">
                            <?php if($modelEmailView): ?>
                                <?=$this->render('_view_mail', ['model' => $modelEmailView])?>
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
/*$js = <<<JS
$(document).on('click', '.view_email', function() {
  var emailId = $(this).data('email-id');
  //alert(emailId);
  //$.pjax.reload({container: '#pjax-view-mail', async:false});
});

//$.pjax({url: 'demo2.html', container: '.container'})
JS;

$this->registerJs($js, \yii\web\View::POS_READY);*/


