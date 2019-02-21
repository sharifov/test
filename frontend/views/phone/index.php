<?php
/* @var $this yii\web\View */

use yii\helpers\Html;
use frontend\assets\AppAsset;
$bundle = AppAsset::register($this);;
//\yii\helpers\VarDumper::dump($bundle, 10, true); exit;
?>
<?php $this->beginPage(); ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta name="description" content="Communication Service">
        <?php
        $this->registerMetaTag(['http-equiv' => 'Content-Type', 'content' => 'text/html; charset=UTF-8']);
        $this->registerMetaTag(['http-equiv' => 'X-UA-Compatible', 'content' => 'IE=edge']);

        $this->registerMetaTag(['charset' => Yii::$app->charset]);
        $this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1']);
        $this->metaTags[] = Html::csrfMetaTags();
        $this->registerMetaTag(['name' => 'robots', 'content' => 'noindex, nofollow']);
        $this->registerMetaTag(['name' => 'msapplication-TileColor', 'content' => '#a9e04b']);
        //$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => Helper::publishStatic('images/favicons/16x16.png'), 'sizes' => '16x16']);
        $this->registerLinkTag(['rel' => 'shortcut icon', 'type' => 'image/x-icon', 'href' => Yii::$app->request->baseUrl.'/favicon.ico']);
        $this->head();
        $host = 'Communication';
        echo Html::tag('title', ucfirst($host).' - '.Html::encode($this->title));
        ?>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="<?= Yii::$app->getAssetManager()->publish(Yii::getAlias('@frontend').'/web/css/quickstart.css')[1];?>"/>
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="nav-md">
    <?php $this->beginBody(); ?>
    <div class="container body">

        <div class="main_container">
            <div id="controls">
                <div id="info">
                    <p class="instructions">Twilio Client</p>
                    <div id="client-name"></div>
                    <div id="output-selection">
                        <label>Ringtone Devices</label>
                        <select id="ringtone-devices" multiple></select>
                        <label>Speaker Devices</label>
                        <select id="speaker-devices" multiple></select><br/>
                        <a id="get-devices">Seeing unknown devices?</a>
                    </div>
                </div>
                <div id="call-controls">
                    <p class="instructions">Make a Call:</p>
                    <input id="phone-number" type="text" placeholder="Enter a phone # or client name" />
                    <button id="button-call">Call</button>
                    <button id="button-hangup">Hangup</button>
                    <div id="volume-indicators">
                        <br />
                        <label>Mic Volume</label>


                        <div id="input-volume" style=" border: dashed 1px #fff; width: 200px; height: 10px;"></div>


                        <br/><br/>
                        <label>Speaker Volume</label>
                        <div id="output-volume" style=" border: dashed 1px #fff; width: 200px; height: 10px;"></div>
                    </div>
                </div>

                <div id="call-controls2">
                    <button id="button-answer">Answer</button>
                    <button id="button-reject">Reject</button>
                </div>
                <div id="log"></div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        var tw_configs = {"client":"<?= $client;?>","FromAgentPhone":"<?= $fromAgentPhone;?>"};
    </script>

    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://media.twiliocdn.com/sdk/js/client/v1.6/twilio.min.js"></script>
    <?= Html::jsFile('/js/quickstart.js') ?>
    </body>
    </html>
<?php $this->endPage(); ?>