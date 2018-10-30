<?php
/**
 * @var $project \common\models\Project
 * @var $agentName string
 * @var $employee \common\models\Employee
 * @var $body string
 * @var $templateType string
 * @var $userProjectParams \common\models\UserProjectParams
 */
?>

<body style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: none; background-color: #EBEFF8; box-sizing: border-box; color: #333; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 15px; font-weight: normal; line-height: 1.3; margin: 0; min-width: 100%; padding: 0; text-align: left; width: 100% !important;">
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="body"
       style="background: #EBEFF8; border-collapse: collapse; border-spacing: 0; color: #1C2F59; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; height: 100%; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
    <tbody>
    <tr style="padding: 0; text-align: left; vertical-align: top;">
        <td class="wrapper-inner" align="center" valign="top"
            style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; color: #1C2F59; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; max-width: 808px !important; min-width: 808px !important; padding: 110px 20px 10px !important; text-align: left; vertical-align: top; width: 808px !important; word-wrap: break-word;">
            <table width="768" align="center" border="0" cellpadding="0" cellspacing="0"
                   class="container content-wrapper main-content"
                   style="background: #fefefe; background-color: #ffffff; border: 1px solid #DDDDDD; border-collapse: collapse; border-radius: 0; border-spacing: 0; margin: 0 auto; margin-bottom: 20px; max-width: 768px !important; min-width: 768px !important; padding: 0; text-align: inherit; vertical-align: top; width: 768px !important;">
                <tbody>
                <tr style="padding: 0; text-align: left; vertical-align: top;">
                    <td class="full-width"
                        style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; color: #1C2F59; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important; word-wrap: break-word;">
                        <table class="full-width"
                               style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important;">

                            <!--Header-->
                            <tbody>
                            <tr style="padding: 0; text-align: left; vertical-align: top;">
                                <td class="header"
                                    style="-moz-hyphens: auto; -webkit-hyphens: auto; border-bottom: 1px solid #F5F7FC; border-collapse: collapse !important; color: #1C2F59; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
                                    <table class="row"
                                           style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                            <th class="small-6 large-6 columns header-logo" valign="middle"
                                                align="center"
                                                style="color: #1C2F59; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; height: 120px; line-height: 1.3; margin: 0 auto; min-height: 120px; padding: 0 40px !important; padding-bottom: 16px; padding-left: 24px; padding-right: 8px; text-align: left; vertical-align: middle !important; width: 50%;">
                                                <img src="<?= $project->link ?>/images/logo-colored.png"
                                                     alt="<?= $project->name ?>"
                                                     width="179"
                                                     height="30" class="logo float-center" align="center"
                                                     style="-ms-interpolation-mode: bicubic; clear: both; display: block; float: none; height: 30px; margin: 0 auto; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: 179px;">
                                            </th>
                                            <th class="small-6 large-6 columns header-agent" valign="middle"
                                                style="background-color: #F5F7FC; color: #1C2F59; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; height: 120px; line-height: 1.3; margin: 0 auto; min-height: 120px; padding: 0 40px !important; padding-bottom: 16px; padding-left: 24px; padding-right: 8px; text-align: left; vertical-align: middle !important; width: 50%;">
                                                <div class="agent-name"
                                                     style="color: #556383; font-size: 14px; height: 16px; line-height: 20px; padding-top: 8px;">
                                                    <?= $agentName ?>
                                                </div>
                                                <div class="agent-phone"
                                                     style="color: #1C2F59; font-size: 24px; font-weight: bold; line-height: 1.2em; text-decoration: none;">
                                                    <?php if ($userProjectParams !== null && $userProjectParams->upp_phone_number) {
                                                        echo $userProjectParams->upp_phone_number;
                                                    } else {
                                                        echo $project->contactInfo->phone;
                                                    } ?>
                                                </div>
                                            </th>
                                            <th class="expander"
                                                style="color: #1C2F59; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left;"></th>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!-- Body  -->
                            <?= $body ?>

                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>

            <table width="768" align="center" border="0" cellpadding="0" cellspacing="0"
                   class="container content-wrapper footer"
                   style="background: #fefefe; background-color: #ffffff; border: 1px solid #DDDDDD; border-collapse: collapse; border-radius: 0; border-spacing: 0; margin: 0 auto; max-width: 768px !important; min-width: 768px !important; padding: 0; text-align: inherit; vertical-align: top; width: 768px !important;">
                <tr style="padding: 0; text-align: left; vertical-align: top;">
                    <td class="full-width"
                        style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; color: #1C2F59; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0 20px; text-align: left; vertical-align: top; width: 100% !important; word-wrap: break-word;">
                        <table class="full-width"
                               style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important;">
                            <tr style="padding: 0; text-align: left; vertical-align: top;">
                                <th class="footer-top"
                                    style="border-bottom: 1px solid #F5F7FC; color: #1C2F59; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; padding: 30px 0; text-align: left;">
                                    <table class="row"
                                           style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                            <th class="small-12 large-12 columns first last"
                                                style="color: #1C2F59; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 24px; padding-right: 24px; text-align: left; width: 100%;">
                                                Sincerely,<br><?= $agentName ?>
                                            </th>
                                        </tr>
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                            <th class="small-12 large-12 columns first last"
                                                style="color: #1C2F59; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 24px; padding-right: 24px; text-align: left; width: 100%;">
                                                <?php if ($userProjectParams !== null) : ?>
                                                    E-Mail: <a href="mailto:<?= $userProjectParams->upp_email ?>"
                                                               class="footer-email"
                                                               style="color: #4ECCC4; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left; text-decoration: none;"><?= $userProjectParams->upp_email ?></a>
                                                    <br>
                                                <?php else : ?>
                                                    E-Mail: <a href="mailto:<?= $employee->email ?>"
                                                               class="footer-email"
                                                               style="color: #4ECCC4; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left; text-decoration: none;"><?= $employee->email ?></a>
                                                    <br>
                                                <?php endif; ?>
                                                <?php if ($userProjectParams !== null && !empty($userProjectParams->upp_phone_number)) {
                                                    echo sprintf('Direct Line: %s <br>', $userProjectParams->upp_phone_number);
                                                } ?>
                                                General line: <?= $project->contactInfo->phone ?>
                                            </th>
                                        </tr>
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                            <th class="small-12 large-12 columns first last"
                                                style="color: #1C2F59; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0 auto; padding: 0; padding-top: 30px;padding-bottom: 16px; padding-left: 24px; padding-right: 24px; text-align: left; width: 100%;">
                                                Share your experience on: <br/>
                                                <?php $trustPilotUrl = (isset($templateType) && $templateType == "_email_ticket")?"https://www.trustpilot.com/evaluate/wowfare.com":"https://www.trustpilot.com/review/wowfare.com"?>
                                                <a href="<?= $trustPilotUrl?>" target="_blank"><img alt="trustpilot logo" src="<?= $project->link ?>/images/email/email-tp-widget.png" style="-ms-interpolation-mode: bicubic; clear: both; display: block; float: none; margin: 20px auto 0; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;"></a>
                                            </th>
                                        </tr>
                                    </table>
                                </th>
                            </tr>

                        </table>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
    <tr style="padding: 0; text-align: left; vertical-align: top;">
                                <th class="footer-bottom"
                                    style="color: #1C2F59; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal; line-height: 1.3; margin: 0; padding: 40px 0 15px; text-align: center;">
                                    <table class="row"
                                           style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                            <th class="small-12 large-12 columns first last" align="center"
                                                style="color: #1C2F59; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0 auto; padding: 0; padding-left: 24px; padding-right: 24px; text-align: left; width: 100%;">
                                                <img src="<?= $project->link ?>/images/email/email-logo-desaturated.png" alt="Logo"
                                                     width="151" height="22"
                                                     class="footer-logo float-center"
                                                     style="-ms-interpolation-mode: bicubic; clear: both; display: block; float: none; margin: 0 auto; margin-bottom: 15px; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;">
                                            </th>
                                            <th class="expander"
                                                style="color: #1C2F59; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left;"></th>
                                        </tr>
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                            <th class="small-12 large-12 columns first last footer-text text-center"
                                                align="center"
                                                style="color: #556383; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.4em; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 24px; padding-right: 24px; text-align: center; width: 100%;">

                                                <div class="footer-copyright" style="color: #556383;">
                                                    © 2018 The Travel Outlet of Virginia, LLC. All rights reserved.
                                                </div>
                                            </th>
                                            <th class="expander"
                                                style="color: #1C2F59; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left;"></th>
                                        </tr>
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                            <th class="small-12 large-12 columns first last" align="center"
                                                style="color: #1C2F59; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 24px; padding-right: 24px; text-align: left; width: 100%;">
                                                <img src="<?= $project->link ?>/images/email/email-partners.png" alt="Partners"
                                                     width="281" height="40"
                                                     class="footer-partners float-center"
                                                     style="-ms-interpolation-mode: bicubic; clear: both; display: block; float: none; margin: 20px auto 0; max-width: 100%; outline: none; text-align: center; text-decoration: none; width: auto;">
                                            </th>
                                            <th class="expander"
                                                style="color: #1C2F59; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left;"></th>
                                        </tr>
                                    </table>
                                </th>
                            </tr>
    </tbody>
</table>
</body>
