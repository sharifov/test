<?php

/* @var $this \yii\web\View */

use yii\helpers\Html;

/** @var \common\models\Employee $user */
$user = Yii::$app->user->identity;
?>
<div class="sidebar-footer hidden-small">
    <div class="col-md-12 form-group" id="search-menu-div" style="display: none">
        <div class="input-group mb-2">
            <div class="input-group-prepend">
                <div class="input-group-text"><i class="fa fa-search"></i></div>
            </div>
            <?=\yii\bootstrap4\Html::input('text', 'search-menu', null, ['class' => 'form-control form-control-sm', 'placeholder' => 'Search menu', 'id' => 'input-search-menu']) ?>
        </div>
    </div>
    <?php /*<a data-toggle="tooltip" data-placement="top" title="Settings">
                        <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                        <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="Lock">
                        <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
                    </a>*/ ?>

    <?php /*=Html::a('<span class="glyphicon glyphicon-off" aria-hidden="true"></span>', ['/site/logout'],
        ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Logout'])*/ ?>


    <?=Html::a(
        '<span class="fa fa-search"></span>',
        null,
        ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Search menu', 'id' => 'btn-search-menu-toggle']
) ?>

    <?php /* if ($user->canRoute('/user-connection/index')) :?>
        <?=Html::a(
            '<span class="fa fa-plug"></span>',
            ['/user-connection/index'],
            ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'User Connections']
        ) ?>
    <?php endif;*/ ?>



    <?php /* if (Yii::$app->user->can('PhoneWidget')) :?>
        <?=Html::a(
            '<span class="fa fa-phone-square"></span>',
            ['/voip/index'],
            ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'VoIP - Phone Device', 'target' => '_blank']
        ) ?>
    <?php endif; */ ?>

    <?php if ($user->canRoute('/call/realtime-map')) :?>
        <?=Html::a(
            '<span class="fa fa-map"></span>',
            ['/call/realtime-map'],
            ['target' => '_blank', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Call realtime Map']
        ) ?>
    <?php endif; ?>

    <?php if ($user->canRoute('/setting/index')) :?>
        <?=Html::a(
            '<span class="fa fa-cogs"></span>',
            ['/setting/index'],
            ['target' => '_blank', 'data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Site Settings']
        ) ?>
    <?php endif; ?>

    <?php /*if ($user->canRoute('/user-connection/index')) :*/?>
    <?=Html::a(
        '<span class="fa fa-bug warning"></span>',
        ['/bug/create'],
        ['data-toggle' => 'tooltip', 'data-placement' => 'top', 'title' => 'Bug Report', 'id' => 'btn-bug-create']
    ) ?>
    <?php /*endif;*/ ?>

</div>
<?php
$sideBarMenuUrl = \yii\helpers\Url::to(['/site/side-bar-menu']);

$js = <<<JS
const sideBarMenuUrl = '$sideBarMenuUrl';

$('#btn-search-menu-toggle').on('click', function (e) {
    e.preventDefault();
    $('#search-menu-div').toggle();
});



    
function canvasEditor() {
   
      const props = {
          usageStatistics: false,
    includeUI: {
      loadImage: {
        path: "https://i1.wp.com/www.tor.com/wp-content/uploads/2018/10/Malazan-Kotaki.jpg?fit=740%2C386&type=vertical&quality=100&ssl=1",
        name: "SampleImage"
      },
      uiSize: {
        width: "100%",
        height: "600px"
      },
      menu: [
        "crop",
        //"flip",
        "rotate",
        "draw",
        "shape",
        "icon",
        "text",
        //"filter"
      ],
      menuBarPosition: "bottom"
      // theme: whiteTheme,
    },
    cssMaxWidth: 1200,
    cssMaxHeight: 800,
    selectionStyle: {
      cornerSize: 20,
      rotatingPointOffset: 70
    }
  };
  
    const imageEditor = new tui.ImageEditor('#tui-image-editor', props);
    //imageEditor.loadImageFromURL('https://cdn.rawgit.com/nhnent/tui.component.image-editor/1.3.0/samples/img/sampleImage.jpg', 'SampleImage');
    
}


$('body').off('click', '#btn-bug-create').on('click', '#btn-bug-create', function (e) {
    e.preventDefault();
    
    let btn = $(this);
    let url = btn.attr('href');
    
    /*let btnIconHtml = btn.find('i')[0];
    let iconSpinner = '<i class="fa fa-spin fa-spinner"></i>';
    
    
    btn.find('i').replaceWith(iconSpinner);
    btn.addClass('disabled');*/
    
    let modal = $('#modal-lg');
    modal.find('.modal-body').html('');
    modal.find('.modal-title').html('Create Bug issue');
    
    
    //$('#modal-label').html('Create Bug issue');
    
    let id = $(this).attr('data-id');
    modal.find('.modal-body').load(url, function( response, status, xhr ) {
        if(status === 'error') {
            createNotify('Error', xhr.responseText, 'error');
        } else {
          modal.modal('show');
          
          const screenshotTarget = document.body;

            html2canvas(screenshotTarget).then((canvas) => {
               const base64image = canvas.toDataURL("image/png");
               
               let str = ''; //'<h3 class=text-center>DrawerJs Demonstration</h3> <div id="tui-image-editor"></div>';
               modal.find('.modal-body').html('<img src="' + base64image + '" style="width:50%"/>' + str);
               // canvasEditor();
               //window.location.href = base64image;
            });
                      
        }
        //btn.find('i').replaceWith(btnIconHtml);
        //btn.removeClass('disabled');
    });
});


// $('#btn-bug-create').on('click', function (e) {
//     e.preventDefault();
//     // $('#input-search-menu').val('');
//     // $('#input-search-menu').trigger('keyup');
//    
//    
//    
//    
//     return false;
// });


$('body').on('click', '#btn-remove-search-menu', function (e) {
    e.preventDefault();
    $('#input-search-menu').val('');
    $('#input-search-menu').trigger('keyup');
    return false;
});


$('#input-search-menu').on('keyup', delay(function (e) {
    let val = $(this).val();
    $.pjax.reload({url: sideBarMenuUrl, container: '#pjax-sidebar-menu', push: false, replace: false, 'scrollTo': false, timeout: 3000, async: false, data: {search_text: val}});
}, 400));

/*$('#input-search-menu').on('keyup', delay (e) {
    let val = $(this).val();
    if (val.length > 0) {
        
    }
    $.pjax.reload({container: '#pjax-sidebar-menu', push: false, replace: false, 'scrollTo': false, timeout: 5000, async: false, data: {search_text: val}});
    //e.preventDefault();
    //$('#search-menu-div').toggle();
});*/

function delay(fn, ms) {
  let timer = 0
  return function(...args) {
    clearTimeout(timer)
    timer = setTimeout(fn.bind(this, ...args), ms || 0)
  }
}

$("#pjax-sidebar-menu").on("pjax:start", function() {
    //$('#input-search-menu').prop('disabled', true);
});
$("#pjax-sidebar-menu").on("pjax:end", function() {
    init_sidebar();
    //$('#input-search-menu').prop('disabled', false).attr('disabled', false);
    //alert(123);
});


JS;
$this->registerJs($js, \yii\web\View::POS_READY);
