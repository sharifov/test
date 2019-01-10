<?php
/* @var $model \common\models\Notifications[] */
/* @var $newCount integer */
?>
<?php yii\widgets\Pjax::begin(['id' => 'notify-pjax', 'timeout' => 10000, 'enablePushState' => false, 'options' => [
        'tag' => 'li',
        'class' => 'dropdown',
        'role' => 'presentation',
]])?>
    <a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-envelope-o"></i>
        <? if($newCount): ?>
        <span class="badge bg-green"><?=$newCount?></span>
        <? endif;?>
    </a>

    <ul id="menu1" class="dropdown-menu list-unstyled msg_list" role="menu">
        <?

        $soundPlay = false;

        if($model)
            foreach ($model as $n => $item): ?>
        <li>
            <a href="<?=\yii\helpers\Url::to(['notifications/view2', 'id' => $item->n_id])?>" data-pjax="0">
                <span class="glyphicon glyphicon-info-sign"> <? //remove-sign, ok-sign, question-sign ?>
                </span>
                <span>
                    <span><?=\yii\helpers\Html::encode($item->n_title)?></span>
                    <span class="time"><?=Yii::$app->formatter->asRelativeTime($item->n_created_dt)?></span>
                </span>
                <span class="message">
                    <?=mb_substr(\yii\helpers\Html::encode($item->n_message), 0, 80)?>...
                </span>
            </a>
            <?
                if($item->n_popup && !$item->n_popup_show):
                $soundPlay = true;

                $message = str_replace("\r\n", "", ($item->n_message));
                $message = str_replace("\n", "", $message);
                $message = str_replace('"', '\"', $message);

                $type = $item->getNotifyType();

                if(!$item->n_popup_show) {
                    $item->n_popup_show = true;
                    $item->save();
                }

                $js2 = '
                new PNotify({
                    title: "'.\yii\helpers\Html::encode($item->n_title).'",
                    type: "'.$type.'",
                    text: "'.$message.'",
                    desktop: {
                        desktop: true
                    },
                    /*nonblock: {
                        nonblock: true
                    },*/
                    delay: 30000,
                    hide: false
                }).get().click(function(e) {
        
                });
                
                new PNotify({
                    title: "'.\yii\helpers\Html::encode($item->n_title).'",
                    type: "'.$type.'",
                    text: "'.$message.'",
                    hide: true
                });
                
                ';

                if($n < 20) $this->registerJs($js2, \yii\web\View::POS_READY);
            ?>
            <? endif;?>
        </li>
        <? endforeach; ?>
        <li>
            <div class="text-center">
                <?=\yii\helpers\Html::a('<strong>See all Notifications</strong>', ['notifications/list'], ['data-pjax' => 0])?>
            </div>

            <?
                if($newCount) $jsDiv = '<span class="label-success label pull-right">'.$newCount.'</span>';
                    else $jsDiv = '';
                $this->registerJs("$('#div-cnt-notification').html('".$jsDiv."')", \yii\web\View::POS_READY);
            ?>
        </li>

    </ul>
    <? if($soundPlay) $this->registerJs('ion.sound.play("door_bell");', \yii\web\View::POS_READY); ?>

<?php yii\widgets\Pjax::end() ?>

<?php
$jsPath = Yii::$app->request->baseUrl.'/js/sounds/';
$js = <<<JS
    PNotify.prototype.options.styling = "bootstrap3";
    PNotify.desktop.permission();
    
    ion.sound({
        sounds: [
            {name: "bell_ring"},
            {name: "door_bell"},
            {name: "button_tiny"}
        ],
        path: "$jsPath",
        preload: true,
        multiplay: true,
        volume: 0.8
    });
   
   
    function updatePjaxNotify() {
        $.pjax({container : '#notify-pjax', push: false, timeout: '6000', scrollTo: false});  
    }
    var timerId2 = setInterval(updatePjaxNotify, 59000);
JS;

//if(Yii::$app->controller->uniqueId)
/*if(in_array(Yii::$app->controller->action->uniqueId, ['orders/create'])) {

} else {*/

    if (Yii::$app->controller->module->id != 'user-management') {
        $this->registerJs($js, \yii\web\View::POS_READY);
    }
//}


