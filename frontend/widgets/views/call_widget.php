<?php


//\frontend\assets\ReactAsset::register($this);
//\frontend\assets\TimerAsset::register($this);

use yii\widgets\Pjax;
\frontend\assets\ReactAsset::register($this);

?>


<?php Pjax::begin(['id' => 'call-widget-pjax', 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false])?>
<?php Pjax::end() ?>


<div id="root"></div>
<script type="text/babel">

    //    alert(123);

    const element = (
        <h1 className="greeting">
            Hello, world фффффф!
        </h1>
    );

    ReactDOM.render(
        element,
        document.getElementById('root')
    );

</script>

<?php
$this->registerJsFile('/js/react/call-widget.js', [
    'position' => \yii\web\View::POS_END,
    'depends' => [
        \frontend\assets\VueAsset::class
    ]
]);
?>


<?php
    $callWidgetUrl = \yii\helpers\Url::to(['/call-widget/index']);
?>

<script>
    const callWidgetUrl = '<?=$callWidgetUrl?>';
    function initCallWidget(obj)
    {
        // console.log(obj);
        $.pjax.reload({url: callWidgetUrl, container: '#call-widget-pjax', push: false, replace: false, 'scrollTo': false, timeout: 10000, async: false}); // , data: {id: obj.id, status: obj.status}
    }
</script>

<?php
$js = <<<JS
   
     initCallWidget();

    //
    //
    // $("#incoming-call-pjax").on("pjax:end", function() {
    //     initIncomingCallWidget();
    // });
    //
    //    
    //
    // function initIncomingCallWidget()
    // {
    //     $('#incoming-call-widget').css({left:'50%', 'margin-left':'-'+($('#call-widget').width() / 2)+'px'}); //.slideDown();
    //     //startTimers();
    //     $('#incoming-call-widget').slideDown();
    // }
    //
    // initIncomingCallWidget();

JS;

//$this->registerJs($js, \yii\web\View::POS_READY);
//$this->registerJs("$('#incoming-call-widget').slideDown();", \yii\web\View::POS_READY);