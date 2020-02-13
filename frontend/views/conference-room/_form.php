<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\ConferenceRoom;

/* @var $this yii\web\View */
/* @var $model common\models\ConferenceRoom */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="conference-room-form">

    <?php $form = ActiveForm::begin(); ?>


    <div class="col-md-3">
        <?= $form->field($model, 'cr_key')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cr_name')->textInput(['maxlength' => true]) ?>

        <?php //= $form->field($model, 'cr_phone_number')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'cr_phone_number')->widget(\borales\extensions\phoneInput\PhoneInput::class, [
            'jsOptions' => [
                'formatOnDisplay' => false,
                'autoPlaceholder' => 'off',
                'customPlaceholder' => '',
                'allowDropdown' => false,
                //'preferredCountries' => ['us'],
                'customContainer' => 'intl-tel-input'
            ]
        ]) ?>

        <?= $form->field($model, 'cr_enabled')->checkbox() ?>
    </div>
    <div class="col-md-3">



    <?php //= $form->field($model, 'cr_start_dt')->textInput() ?>

    <?= $form->field($model, 'cr_start_dt')->widget(\dosamigos\datetimepicker\DateTimePicker::class, [
        'language' => 'en',
        'size' => 'ms',
        //'template' => '{input}',
        'pickButtonIcon' => 'glyphicon glyphicon-time',
        //'inline' => true,
        'clientOptions' => [
            //'startView' => 1,
            //'minView' => 0,
            //'maxView' => 1,
            'autoclose' => true,
            'format' => 'yyyy-mm-dd hh:ii',
            'linkFormat' => 'HH:ii P', // if inline = true
            // 'format' => 'HH:ii P', // if inline = false
            'todayBtn' => true
        ]
    ]);
    ?>


    <?= $form->field($model, 'cr_end_dt')->widget(\dosamigos\datetimepicker\DateTimePicker::class, [
        'language' => 'en',
        'size' => 'ms',
        //'template' => '{input}',
        'pickButtonIcon' => 'glyphicon glyphicon-time',
        //'inline' => true,
        'clientOptions' => [
            //'startView' => 1,
            //'minView' => 0,
            //'maxView' => 1,
            'autoclose' => true,
            'format' => 'yyyy-mm-dd hh:ii',
            'linkFormat' => 'HH:ii P', // if inline = true
            // 'format' => 'HH:ii P', // if inline = false
            'todayBtn' => true
        ]
    ]);
    ?>





    <?php //= $form->field($model, 'cr_end_dt')->textInput() ?>


    <?php //= $form->field($model, 'cr_moderator_phone_number')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'cr_moderator_phone_number')->widget(\borales\extensions\phoneInput\PhoneInput::class, [
        'jsOptions' => [
            'formatOnDisplay' => false,
            'autoPlaceholder' => 'off',
            'customPlaceholder' => '',
            'allowDropdown' => false,
            //'preferredCountries' => ['us'],
            'customContainer' => 'intl-tel-input'
        ]
    ]) ?>

    <?= $form->field($model, 'cr_welcome_message')->textarea(['rows' => 6]) ?>

    </div>

    <div class="col-md-3">
        <?= $form->field($model, 'cr_param_muted')->checkbox() ?>

        <?= $form->field($model, 'cr_param_start_conference_on_enter')->checkbox() ?>

        <?= $form->field($model, 'cr_param_end_conference_on_exit')->checkbox() ?>

        <?= $form->field($model, 'cr_param_beep')->dropDownList(ConferenceRoom::getParamBeepList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'cr_param_max_participants')->input('number', ['min' => 1, 'max' => 255]) ?>
        <p>positive integer <= 250</p>
    </div>
    <div class="col-md-3">



        <?= $form->field($model, 'cr_param_record')->dropDownList(ConferenceRoom::getParamRecordList(), ['prompt' => '---']) ?>
        <p>do-not-record or record-from-start</p>

        <?= $form->field($model, 'cr_param_region')->dropDownList(ConferenceRoom::getParamRegionList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'cr_param_trim')->dropDownList(ConferenceRoom::getParamTrimList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'cr_param_wait_url')->textInput(['maxlength' => true]) ?>

        <p>TwiML URL, empty string	(default Twilio hold music)</p>


    <!---->
    <!--    --><?php //= $form->field($model, 'cr_created_dt')->textInput() ?>
    <!---->
    <!--    --><?php //= $form->field($model, 'cr_updated_dt')->textInput() ?>
    <!---->
    <!--    --><?php //= $form->field($model, 'cr_created_user_id')->textInput() ?>
    <!---->
    <!--    --><?php //= $form->field($model, 'cr_updated_user_id')->textInput() ?>


    </div>
    <div class="col-md-12">
        <div class="form-group text-center">
            <?= Html::submitButton('<i class="fa fa-check-square-o"></i> Save Conference Room', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <div class="clearfix"></div>
    <hr>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2><i class="fa fa-info-circle"></i> Help info</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li>
                            <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content" style="display: none">
                    <div class="markdown">
                        <p>The <a href="dial"><code>&lt;Dial&gt;</code></a> verb's <code>&lt;Conference&gt;</code> noun allows you to connect to a conference
                            room. Much like how the <a href="dial#nouns-number"><code>&lt;Number&gt;</code></a> noun allows you to connect to another phone
                            number, the <code>&lt;Conference&gt;</code> noun allows you to connect to a named conference
                            room and talk with the other callers who have also connected to that room. Conference is commonly used as a container for calls when implementing hold, transfer, and barge.</p>
                        <p>Twilio offers a globally distributed, low latency conference system that hosts your conferences in the region closest to the majority
                            of your participants and has a maximum participant capacity of 250. It has a
                            per-participant-per-minute price in addition to standard voice minute pricing.
                        </p>
                        <h4 class="docs-article__section-title" id="customizable-features"><a class="toclink" href="#customizable-features">Customizable Features</a></h4>
                        <p>The name of the room is up to you and is namespaced to your account. This
                            means that any caller who joins <code>room1234</code> via your account will end up in
                            the same conference room, but callers connecting through different accounts would not.</p>
                        <p><strong>Note:</strong> <strong><em> For compliance reasons, do not use personal data (also known as personally identifiable information), such as phone numbers, email addresses, or a person’s name, or any other sensitive information when naming the conferences </em></strong></p>
                        <p>By default, Twilio conference rooms enable a number of useful features that can
                            be enabled or disabled based on your particular needs:</p>
                        <ul class="docs-article__list">
                            <li>Conferences will not start until at least two participants join.</li>
                            <li>While waiting, customizable background music is played.</li>
                            <li>When participants join and leave, notification sounds are played to inform the other participants.</li>
                            <li>Events can be configured to alert your application to state changes in a conference</li>
                            <li>Beta Feature: receive a webhook when a participant speaks or stops speaking</li>
                        </ul>
                        <p>You can configure or disable each of these features based on your particular needs.</p>
                        <h3 class="docs-article__section-title" id="attributes"><a class="toclink" href="#attributes">Noun Attributes</a></h3>
                        <p>The <code>&lt;Conference&gt;</code> noun supports the following attributes that modify its behavior:</p>
                        <div class="twilio-table-wrapper"><table>
                                <thead>
                                <tr>
                                    <th align="left">Attribute Name</th>
                                    <th align="left">Allowed Values</th>
                                    <th align="left">Default Value</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td align="left"><a href="#attributes-muted">muted</a></td>
                                    <td align="left">true, false</td>
                                    <td align="left">false</td>
                                </tr>
                                <tr>
                                    <td align="left"><a href="#attributes-beep">beep</a></td>
                                    <td align="left">true, false, onEnter, onExit</td>
                                    <td align="left">true</td>
                                </tr>
                                <tr>
                                    <td align="left"><a href="#attributes-startConferenceOnEnter">startConferenceOnEnter</a></td>
                                    <td align="left">true, false</td>
                                    <td align="left">true</td>
                                </tr>
                                <tr>
                                    <td align="left"><a href="#attributes-endConferenceOnExit">endConferenceOnExit</a></td>
                                    <td align="left">true, false</td>
                                    <td align="left">false</td>
                                </tr>
                                <tr>
                                    <td align="left"><a href="#attributes-waitUrl">waitUrl</a></td>
                                    <td align="left">TwiML URL, empty string</td>
                                    <td align="left">default Twilio hold music</td>
                                </tr>
                                <tr>
                                    <td align="left"><a href="#attributes-waitMethod">waitMethod</a></td>
                                    <td align="left">GET or POST</td>
                                    <td align="left">POST</td>
                                </tr>
                                <tr>
                                    <td align="left"><a href="#attributes-maxParticipants">maxParticipants</a></td>
                                    <td align="left">positive integer &lt;= 250</td>
                                    <td align="left">250</td>
                                </tr>
                                <tr>
                                    <td align="left"><a href="#record">record</a></td>
                                    <td align="left">do-not-record or record-from-start</td>
                                    <td align="left">do-not-record</td>
                                </tr>
                                <tr>
                                    <td align="left"><a href="#attributes-region">region</a></td>
                                    <td align="left">us1, ie1, de1, sg1, br1, au1, jp1</td>
                                    <td align="left">None</td>
                                </tr>
                                <tr>
                                    <td align="left"><a href="#attributes-trim">trim</a></td>
                                    <td align="left">trim-silence or do-not-trim</td>
                                    <td align="left">trim-silence</td>
                                </tr>
                                <tr>
                                    <td align="left"><a href="#attributes-coach">coach</a></td>
                                    <td align="left">A Call SID</td>
                                    <td align="left">none</td>
                                </tr>
                                </tbody>
                            </table></div>
                        <h4 class="docs-article__section-title" id="attributes-muted"><a class="toclink" href="#attributes-muted">muted</a></h4>
                        <p>The <code>muted</code> attribute lets you specify whether a participant can speak on the
                            conference. If this attribute is set to <code>true</code>, the participant will only be
                            able to listen to people on the conference. This attribute defaults to <code>false</code>.</p>
                        <p>To change a conference participant's muted attribute during
                            a call use to the <a href="https://www.twilio.com/docs/voice/api/conference-participant">Conference Participant API</a>.</p>
                        <h4 class="docs-article__section-title" id="attributes-beep"><a class="toclink" href="#attributes-beep">beep</a></h4>
                        <p>The <code>beep</code> attribute lets you specify whether a notification beep is played to
                            the conference when a participant joins or leaves the conference. Defaults to <code>true</code>.</p>
                        <div class="twilio-table-wrapper"><table>
                                <thead>
                                <tr>
                                    <th align="left">Value</th>
                                    <th align="left">Behavior</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td align="left">true</td>
                                    <td align="left">Default. Plays a beep both when a participant joins and when a participant leaves.</td>
                                </tr>
                                <tr>
                                    <td align="left">false</td>
                                    <td align="left">Disables beeps for when participants both join and exit.</td>
                                </tr>
                                <tr>
                                    <td align="left">onEnter</td>
                                    <td align="left">Only plays a beep when a participant joins. The beep will not be played when the participant exits.</td>
                                </tr>
                                <tr>
                                    <td align="left">onExit</td>
                                    <td align="left">Will not play a beep when a participant joins; only plays a beep when the participant exits.</td>
                                </tr>
                                </tbody>
                            </table></div>
                        <h4 class="docs-article__section-title" id="attributes-startConferenceOnEnter"><a class="toclink" href="#attributes-startConferenceOnEnter">startConferenceOnEnter</a></h4>
                        <p>This attribute tells a conference to start when this participant joins the
                            conference, if it is not already started. This is <code>true</code> by default. If this is
                            <code>false</code> and the participant joins a conference that has not started, they are
                            muted and hear background music until a participant joins where
                            startConferenceOnEnter is <code>true</code>. This is useful for implementing moderated
                            conferences.</p>
                        <h4 class="docs-article__section-title" id="attributes-endConferenceOnExit"><a class="toclink" href="#attributes-endConferenceOnExit">endConferenceOnExit</a></h4>
                        <p>If a participant has this attribute set to <code>true</code>, then when that participant
                            leaves, the conference ends and all other participants drop out. This
                            defaults to <code>false</code>. This is useful for implementing moderated conferences that
                            bridge two calls and allow either call leg to continue executing TwiML if the
                            other hangs up.</p>
                        <h4 class="docs-article__section-title" id="attributes-waitUrl"><a class="toclink" href="#attributes-waitUrl">waitUrl</a></h4>
                        <p>The 'waitUrl' attribute lets you specify a URL for music that plays before the
                            conference has started. The URL may be an MP3, a WAV or a TwiML document that uses
                            <a href="play"><code>&lt;Play&gt;</code></a> or <a href="say"><code>&lt;Say&gt;</code></a> for content. This defaults to a selection of Creative Commons
                            licensed background music, but you can replace it with your own music and
                            messages. If the 'waitUrl' responds with TwiML, Twilio will only process <a href="play"><code>&lt;Play&gt;</code></a>,
                            <a href="say"><code>&lt;Say&gt;</code></a>, and <a href="redirect"><code>&lt;Redirect&gt;</code></a> verbs.  <a href="record"><code>&lt;Record&gt;</code></a>,
                            <a href="dial"><code>&lt;Dial&gt;</code></a>, and <a href="gather"><code>&lt;Gather&gt;</code></a> verbs are not allowed. If you do not wish
                            anything to play while waiting for the conference to start, specify the empty string
                            (set 'waitUrl' to '').</p>
                        <p>If no 'waitUrl' is specified, Twilio will use its own <a href="http://labs.twilio.com/twimlets/holdmusic">HoldMusic Twimlet</a></p>
                        that reads a public <a href="http://s3.amazonaws.com">AWS S3 Bucket</a> for audio files. The default 'waitUrl' is:
                        <p><a href="http://twimlets.com/holdmusic?Bucket=com.twilio.music.classical">http://twimlets.com/holdmusic?Bucket=com.twilio.music.classical</a></p>
                        <p>This URL points at S3 bucket <a href="http://com.twilio.music.classical.s3.amazonaws.com/">com.twilio.music.classical</a>, containing a selection of nice Creative Commons classical music. Here's a list of
                            S3 buckets we've assembled with other genres of music for you to choose from:</p>
                        <div class="twilio-table-wrapper"><table>
                                <thead>
                                <tr>
                                    <th align="left">Bucket</th>
                                    <th align="left">Twimlet URL</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td align="left"><a href="http://com.twilio.music.classical.s3.amazonaws.com/">com.twilio.music.classical</a></td>
                                    <td align="left"><a href="http://twimlets.com/holdmusic?Bucket=com.twilio.music.classical">http://twimlets.com/holdmusic?Bucket=com.twilio.music.classical</a></td>
                                </tr>
                                <tr>
                                    <td align="left"><a href="http://com.twilio.music.ambient.s3.amazonaws.com/">com.twilio.music.ambient</a></td>
                                    <td align="left"><a href="http://twimlets.com/holdmusic?Bucket=com.twilio.music.ambient">http://twimlets.com/holdmusic?Bucket=com.twilio.music.ambient</a></td>
                                </tr>
                                <tr>
                                    <td align="left"><a href="http://com.twilio.music.electronica.s3.amazonaws.com/">com.twilio.music.electronica</a></td>
                                    <td align="left"><a href="http://twimlets.com/holdmusic?Bucket=com.twilio.music.electronica">http://twimlets.com/holdmusic?Bucket=com.twilio.music.electronica</a></td>
                                </tr>
                                <tr>
                                    <td align="left"><a href="http://com.twilio.music.guitars.s3.amazonaws.com/">com.twilio.music.guitars</a></td>
                                    <td align="left"><a href="http://twimlets.com/holdmusic?Bucket=com.twilio.music.guitars">http://twimlets.com/holdmusic?Bucket=com.twilio.music.guitars</a></td>
                                </tr>
                                <tr>
                                    <td align="left"><a href="http://com.twilio.music.rock.s3.amazonaws.com/">com.twilio.music.rock</a></td>
                                    <td align="left"><a href="http://twimlets.com/holdmusic?Bucket=com.twilio.music.rock">http://twimlets.com/holdmusic?Bucket=com.twilio.music.rock</a></td>
                                </tr>
                                <tr>
                                    <td align="left"><a href="http://com.twilio.music.soft-rock.s3.amazonaws.com/">com.twilio.music.soft-rock</a></td>
                                    <td align="left"><a href="http://twimlets.com/holdmusic?Bucket=com.twilio.music.soft-rock">http://twimlets.com/holdmusic?Bucket=com.twilio.music.soft-rock</a></td>
                                </tr>
                                </tbody>
                            </table></div>
                        <h4 class="docs-article__section-title" id="attributes-waitMethod"><a class="toclink" href="#attributes-waitMethod">waitMethod</a></h4>
                        <p>This attribute indicates which HTTP method to use when requesting 'waitUrl'. It
                            defaults to 'POST'. Be sure to use 'GET' if you are directly requesting static
                            audio files such as WAV or MP3 files so that Twilio properly caches the files.</p>
                        <h4 class="docs-article__section-title" id="attributes-maxParticipants"><a class="toclink" href="#attributes-maxParticipants">maxParticipants</a></h4>
                        <p>This attribute indicates the maximum number of participants you want to allow
                            within a named conference room. The maximum number of participants is 250.</p>
                        <h4 class="docs-article__section-title" id="record"><a class="toclink" href="#record">record</a></h4>
                        <p>The 'record' attribute lets you record an entire <code>&lt;conference&gt;</code>. When set to
                            <code>record-from-start</code>, the recording begins when the first two participants are
                            bridged. The hold music is never recorded.  If a 'recordingStatusCallback' URL is given,
                            Twilio will make a request to the specified URL with recording details when the recording is available to access. </p>
                        <h4 class="docs-article__section-title" id="attributes-region"><a class="toclink" href="#attributes-region">region</a></h4>
                        <p>The 'region' attribute specifies the region where Twilio should mix the conference. Specifying a value for region overrides Twilio's automatic region selection logic and should only be used if you are confident you understand where your conferences should be mixed. Twilio sets the region parameter from the first participant that specifies the parameter and will ignore the parameter from subsequent participants.</p>
                        <h4 class="docs-article__section-title" id="attributes-trim"><a class="toclink" href="#attributes-trim">trim</a></h4>
                        <p>The 'trim' attribute lets you specify whether to trim leading and trailing
                            silence from your audio files. 'trim' defaults to <code>trim-silence</code>, which removes
                            any silence at the beginning or end of your recording. This may cause the
                            duration of the recording to be slightly less than the duration of the call.</p>
                        <h4 class="docs-article__section-title" id="attributes-coach"><a class="toclink" href="#attributes-coach">coach</a></h4>
                        <p>Coach accepts a call SID of a call that is currently connected to an in-progress conference. Specifying a call SID that does not exist or is no longer connected to the conference will result in the call failing to the action URL and throwing a 13240 error. Coach is a feature of Agent Conference, which can be enabled via the <a href="https://www.twilio.com/console/voice/settings/conferences">Twilio Console</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
