<?php

use common\models\Employee;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\widgets\ListView;
use yii\helpers\Url;
use src\entities\email\helpers\EmailFilterType;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmailSearch */
/* @var $modelEmailView common\models\Email */
/* @var $modelNewEmail common\models\Email */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $mailList [] */
/* @var $projectList [] */
/* @var $selectedId int */
/* @var $stats [] */
/* @var $action string|null */
/* @var $emailForm src\entities\email\form\EmailForm */

$this->title = 'Emails';
$this->params['breadcrumbs'][] = $this->title;

/** @var Employee $user */
$user = Yii::$app->user->identity;

$is_admin = $user->isAdmin();

?>

<div class="">
    <div class="row">

        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>My Inbox<small>User Mail</small></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">

                    <?php Pjax::begin(['id' => 'pjax-email']); ?>

                    <div class="row">
                        <div class="col-md-3 mail_list_column">

                            <?= Html::beginForm(Url::current(['email_type_id' => null, 'email_project_id' => null, 'email_email' => null ,'action' => null]), 'GET', ['data-pjax' => 1]) ?>
                                <div class="col-md-12" style="margin-bottom: 10px;">
                                    <!-- Split button -->
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-envelope"></i> Create NEW <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php foreach ($mailList as $mailName) : ?>
                                                <li>
                                                    <?=Html::a('<i class="fa fa-envelope"></i> ' . $mailName, Url::current(['email_email' => $mailName, 'id' => null, 'action' => 'create']))?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <?=Html::dropDownList('email_type_id', Yii::$app->request->get('email_type_id'), EmailFilterType::getList(), ['class' => 'form-control', 'onchange' => '$("#btn-submit-email").click();'])?>
                                </div>
                                <div class="col-md-6">
                                    <?php if ($is_admin) :?>
                                        <?=Html::dropDownList('email_project_id', Yii::$app->request->get('email_project_id'), $projectList, ['prompt' => 'All projects', 'class' => 'form-control', 'onchange' => '$("#btn-submit-email").click();'])?>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6" style="margin-top: 10px;">
                                    <?=Html::dropDownList('email_email', Yii::$app->request->get('email_email'), $mailList, ['prompt' => 'All emails', 'class' => 'form-control', 'onchange' => '$("#btn-submit-email").click();'])?>
                                </div>
                                <div class="col-md-6" style="margin-top: 10px;">
                                    <?= Html::submitButton('Ok', ['id' => 'btn-submit-email', 'class' => 'btn btn-primary hidden']) ?>
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
                                'itemView' => function ($model, $key, $index, $widget) use ($selectedId) {
                                    return $this->render('_list_item', ['model' => $model, 'selectedId' => $selectedId]);
                                },
                                'itemOptions' => [
                                    'tag' => false,
                                ],
                            ]) ?>
                        </div>
                        <!-- /MAIL LIST -->

                        <!-- CONTENT MAIL -->
                        <div class="col-sm-9 mail_view">
                            <?php if ($modelEmailView) : ?>
                                <?=$this->render('_view_mail', ['model' => $modelEmailView])?>
                            <?php elseif ($action) : ?>
                                 <?=$this->render('_new_mail', ['emailForm' => $emailForm , 'mailList' => $mailList, 'action' => $action])?>
                            <?php else : ?>
                                 <?=$this->render('_stats', ['stats' => $stats, 'mailList' => $mailList])?>
                            <?php endif; ?>

                        </div>
                        <!-- /CONTENT MAIL -->
                    </div>


                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
$("#pjax-email").on("pjax:start", function() {
    $('#preloader').show(); //fadeOut('slow');
});
$("#pjax-email").on("pjax:end", function() {
    $('#preloader').hide(); //fadeIn('slow');
});
JS;
