<?php

/**
 * @var $centrifugoUrl
 * @var $token
 * @var $channels
 * @var $this yii\web\View
 */

$passChannelsToJs ='["' . implode('", "', $channels) . '"]';

$js = <<<JS
let channels = $passChannelsToJs;
var centrifuge = new Centrifuge('$centrifugoUrl');
centrifuge.setToken('$token');

channels.forEach(channelConnector)

function channelConnector(chName)
{   
    console.log(chName)
    centrifuge.subscribe(chName, function(message) {    
        let messageObj = JSON.parse(message.data.message);
        console.log("Test data " + messageObj)
    });
}

centrifuge.connect();

centrifuge.on('connect', function(context) {        
    console.info('Client connected to Centrifugo and authorized')    
});
    
JS;
$this->registerJs($js, \yii\web\View::POS_LOAD);

?>

<div id="client-chat-page" class="col-md-12">
    <!--<div class="row">

    </div>-->

    <div class="col-md-12">
        <div class="x_panel">
            <div class="x_title">
                <h2>Daily active users <small>Sessions</small></h2>
                <ul class="nav navbar-right panel_toolbox">
                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#">Settings 1</a>
                            <a class="dropdown-item" href="#">Settings 2</a>
                        </div>
                    </li>
                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                    </li>
                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">
                <ul class="list-unstyled timeline">
                    <li>
                        <div class="block client-chat-monitor-message-block">
                            <div class="tags">   <!--list-unstyled top_profiles scroll-view-->
                                <a href="" class="tag">
                                    <span>OVAGO Channel Support</span>
                                </a>
                                <ul class="">
                                    <li class="media event">
                                        <a class="pull-left border-aero profile_thumb">
                                            <i class="fa fa-comment aero"></i>
                                        </a>
                                        <div class="media-body">
                                            <a class="title" href="#">Ms. Mary Jane</a>
                                            <p><strong>$2300. </strong> Agent Avarage Sales </p>
                                            <p> <small>12 Sales Today</small>
                                            </p>
                                        </div>
                                    </li>
                                    <!--<li class="media event">
                                        <a class="pull-left border-green profile_thumb">
                                            <i class="fa fa-user green"></i>
                                        </a>
                                        <div class="media-body">
                                            <a class="title" href="#">Ms. Mary Jane</a>
                                            <p><strong>$2300. </strong> Agent Avarage Sales </p>
                                            <p> <small>12 Sales Today</small>
                                            </p>
                                        </div>
                                    </li>-->
                                </ul>
                            </div>

                            <div class="block_content">
                                <h2 class="title">
                                    <a>Who Needs Sundance When You’ve Got&nbsp;Crowdfunding?</a>
                                </h2>
                                <div class="byline">
                                    <span>13 hours ago</span> by <a>Jane Smith</a>
                                </div>
                                <p class="excerpt">Film festivals used to be do-or-die moments for movie makers. They were where you met the producers that could fund your project, and if the buyers liked your flick, they’d pay to Fast-forward and… <a>Read&nbsp;More</a>
                                </p>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="block client-chat-monitor-message-block">
                            <div class="tags">
                                <a href="" class="tag">
                                    <span>ARANGRANT Channel Support</span>
                                </a>
                                <ul class="">
                                    <li class="media event">
                                        <a class="pull-left border-aero profile_thumb">
                                            <i class="fa fa-comment aero"></i>
                                        </a>
                                        <div class="media-body">
                                            <a class="title" href="#"> Jack Sparrow</a>
                                            <p><strong>$2300. </strong> Agent Avarage Sales </p>
                                            <p> <small>12 Sales Today</small>
                                            </p>
                                        </div>
                                    </li>
                            </div>
                            <div class="block_content">
                                <h2 class="title">
                                    <a>Who Needs Sundance When You’ve Got&nbsp;Crowdfunding?</a>
                                </h2>
                                <div class="byline">
                                    <span>13 hours ago</span> by <a>Jane Smith</a>
                                </div>
                                <p class="excerpt">Film festivals used to be do-or-die moments for movie makers. They were where you met the producers that could fund your project, and if the buyers liked your flick, they’d pay to Fast-forward and… <a>Read&nbsp;More</a>
                                </p>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="block client-chat-monitor-message-block">
                            <div class="tags">
                                <a href="" class="tag">
                                    <span>OVAGO Channel Sales</span>
                                </a>
                                <ul class="">
                                    <li class="media event">
                                        <a class="pull-left border-aero profile_thumb">
                                            <i class="fa fa-comment aero"></i>
                                        </a>
                                        <div class="media-body">
                                            <a class="title" href="#">Mark Crane</a>
                                            <p><strong>$2300. </strong> Agent Avarage Sales </p>
                                            <p> <small>12 Sales Today</small>
                                            </p>
                                        </div>
                                    </li>
                            </div>
                            <div class="block_content">
                                <h2 class="title">
                                    <a>Who Needs Sundance When You’ve Got&nbsp;Crowdfunding?</a>
                                </h2>
                                <div class="byline">
                                    <span>13 hours ago</span> by <a>Jane Smith</a>
                                </div>
                                <p class="excerpt">Film festivals used to be do-or-die moments for movie makers. They were where you met the producers that could fund your project, and if the buyers liked your flick, they’d pay to Fast-forward and… <a>Read&nbsp;More</a>
                                </p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
