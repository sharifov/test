<?php
/**
 * @var $project \common\models\Project
 * @var $agentName string
 * @var $employee \common\models\Employee
 * @var $body string
 * @var $sellerContactInfo \common\models\EmployeeContactInfo
 * @var $userProjectParams \common\models\UserProjectParams
 */
?>

<body style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: none; background-color: #EBEFF8; box-sizing: border-box; color: #333; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 15px; font-weight: normal; line-height: 1.3; margin: 0; min-width: 100%; padding: 0; text-align: left; width: 100% !important;">
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="body"
       style="background: #eff2f9; background-color: #F2F6F7; border-collapse: collapse; border-spacing: 0; color: #0a0a0a; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 14px; font-weight: normal; height: 100%; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; width: 100%;">
    <tr style="padding: 0; text-align: left; vertical-align: top;">
        <td class="wrapper-inner" align="center" valign="top"
            style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; color: #0a0a0a; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; max-width: 808px !important; min-width: 808px !important; padding: 40px 20px !important; text-align: left; vertical-align: top; width: 808px !important; word-wrap: break-word;">
            <table width="768" align="center" border="0" cellpadding="0" cellspacing="0"
                   class="container content-wrapper main-content"
                   style="background: #fefefe; background-color: #ffffff; border: 1px solid #DDDDDD; border-collapse: collapse; border-radius: 0; border-spacing: 0; margin: 0 auto; margin-bottom: 20px; max-width: 768px !important; min-width: 768px !important; padding: 0; text-align: inherit; vertical-align: top; width: 768px !important;">
                <tr style="padding: 0; text-align: left; vertical-align: top;">
                    <td class="full-width"
                        style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; color: #0a0a0a; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important; word-wrap: break-word;">
                        <table class="full-width"
                               style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important;">

                            <!------------------------
                            -----------Header---------
                            ------------------------->

                            <tr style="padding: 0; text-align: left; vertical-align: top;">
                                <td class="header"
                                    style="-moz-hyphens: auto; -webkit-hyphens: auto; border-bottom: 1px solid #F5F7FC; border-collapse: collapse !important; color: #0a0a0a; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
                                    <table class="row"
                                           style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                            <th class="small-6 large-6 columns header-logo" valign="middle"
                                                style="background-color: #212133; color: #0a0a0a; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; height: 70px; line-height: 1.3; margin: 0 auto; min-height: 70px; padding: 0 32px !important; padding-bottom: 16px; padding-left: 24px; padding-right: 8px; text-align: left; vertical-align: middle !important; width: 50%;">
                                                <img src="<?= $project->link ?>/images/logo.png" alt="Tojour" width="83"
                                                     height="30" class="logo" align="center"
                                                     style="-ms-interpolation-mode: bicubic; clear: both; display: block; height: 30px; max-width: 100%; outline: none; text-decoration: none; width: 83px;">
                                            </th>
                                            <th class="small-6 large-6 columns header-agent" valign="middle"
                                                style="background-color: #212133; color: #0a0a0a; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; height: 70px; line-height: 1.3; margin: 0 auto; min-height: 70px; padding: 0 32px !important; padding-bottom: 16px; padding-left: 24px; padding-right: 8px; text-align: right; vertical-align: middle !important; width: 50%;">
                                                <div class="agent-name"
                                                     style="color: #fff; font-size: 14px; height: 16px; line-height: 20px; padding-bottom: 4px;"><?= $agentName ?></div>
                                                <div class="agent-phone"
                                                     style=";text-decoration: none; color: #FEB562; font-size: 20px; font-weight: bold; line-height: 1.2em;">
                                                    <?php if ($userProjectParams !== null && !empty($userProjectParams->upp_phone_number)) {
                                                        echo $userProjectParams->upp_phone_number;
                                                    } else {
                                                        echo $project->contactInfo->phone;
                                                    } ?>
                                                </div>
                                            </th>
                                            <th class="expander"
                                                style="color: #0a0a0a; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left;"></th>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <!------------------------
                            --------   Cover   -------
                            ------------------------->

                            <?= $body ?>


                            <!--Additional Notes-->

                            <tr style="padding: 0; text-align: left; vertical-align: top;">
                                <th class="additional"
                                    style="color: #0a0a0a; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0; padding: 36px 0 24px; text-align: left;">
                                    <table class="row"
                                           style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                            <th class="small-6 large-6 first columns additional-left" valign="top"
                                                style="border-right: 1px solid #F5F7FC; color: #0a0a0a; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 24px; padding-right: 8px; text-align: left; width: 50%;">
                                                <table class="full-width"
                                                       style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important;">
                                                    <tr style="padding: 0; text-align: left; vertical-align: top;">
                                                        <td style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; color: #0a0a0a; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0; text-align: left; vertical-align: top; word-wrap: break-word;">
                                                            <img src="<?= $project->link ?>/images/icn-time.png" width="70"
                                                                 height="70" class="additional-icn float-left"
                                                                 style="-ms-interpolation-mode: bicubic; clear: both; display: block; float: left; height: 70px; margin-right: 20px; max-width: 100%; outline: none; text-align: left; text-decoration: none; width: 70px;">
                                                            <div class="additional-fares"
                                                                 style="font-size: 12px; font-weight: bold; line-height: 1.3em; margin-bottom: 6px;">
                                                                Fares are not guaranteed until tickets are issued.
                                                            </div>
                                                            <div class="additional-fares-note"
                                                                 style="color: #666666; font-size: 12px; line-height: 1.3em;">
                                                                I encourage you to call me with any questions you have.
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </th>
                                            <th class="small-6 large-6 first columns additional-right" valign="top"
                                                style="color: #0a0a0a; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 24px; padding-right: 8px; text-align: left; width: 50%;">
                                                <table class="full-width"
                                                       style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important;">
                                                    <tr style="padding: 0; text-align: left; vertical-align: top;">
                                                        <img src="<?= $project->link ?>/images/icn-support.png" width="70"
                                                             height="70" class="additional-icn float-left"
                                                             style="-ms-interpolation-mode: bicubic; clear: both; display: block; float: left; height: 70px; margin-right: 20px; max-width: 100%; outline: none; text-align: left; text-decoration: none; width: 70px;">
                                                        <div class="additional-book" style="font-size: 12px;">To book or
                                                            for additional options
                                                            call:
                                                        </div>
                                                        <div class="additional-phone">
                                                            <a href="#"
                                                               style="color: #feb562; display: inline-block; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 18px; font-weight: bold; line-height: 1.3; margin: 0; margin-bottom: 5px; padding: 0; text-align: left; text-decoration: none;">
                                                                <?= $project->contactInfo->phone  ?>
                                                            </a>
                                                        </div>
                                                        <div class="additional-contact"
                                                             style="color: #666666; font-size: 12px;">or contact me 24/7
                                                            by e-mail
                                                        </div>
                                                        <div class="additional-email">
                                                            <a href="mailto:<?= $userProjectParams->upp_email ?>"
                                                               class="email-link"
                                                               style="color: #3C63BC; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left; text-decoration: underline;"><?= $userProjectParams->upp_email ?></a>
                                                        </div>
                                                    </tr>
                                                </table>
                                            </th>
                                        </tr>
                                    </table>
                                </th>
                            </tr>

                        </table>
                    </td>
                </tr>
            </table>
            <table width="768" align="center" border="0" cellpadding="0" cellspacing="0"
                   class="container content-wrapper footer"
                   style="background: #fefefe; background-color: #ffffff; border: 1px solid #DDDDDD; border-collapse: collapse; border-radius: 0; border-spacing: 0; margin: 0 auto; max-width: 768px !important; min-width: 768px !important; padding: 0; text-align: inherit; vertical-align: top; width: 768px !important;">
                <tr style="padding: 0; text-align: left; vertical-align: top;">
                    <td class="full-width"
                        style="-moz-hyphens: auto; -webkit-hyphens: auto; border-collapse: collapse !important; color: #0a0a0a; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; hyphens: auto; line-height: 1.3; margin: 0; padding: 0 20px; text-align: left; vertical-align: top; width: 100% !important; word-wrap: break-word;">
                        <table class="full-width"
                               style="border-collapse: collapse; border-spacing: 0; padding: 0; text-align: left; vertical-align: top; width: 100% !important;">
                            <tr style="padding: 0; text-align: left; vertical-align: top;">
                                <th class="footer-top"
                                    style="border-bottom: 1px solid #F5F7FC; color: #0a0a0a; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 16px; font-weight: normal; line-height: 1.3; margin: 0; padding: 30px 0; text-align: left;">
                                    <table class="row"
                                           style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                            <th class="small-12 large-12 columns first last"
                                                style="color: #0a0a0a; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 24px; padding-right: 24px; text-align: left; width: 100%;">
                                                Sincerely,<br><?= $agentName ?>
                                            </th>
                                        </tr>
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                            <th class="small-12 large-12 columns first last"
                                                style="color: #0a0a0a; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 24px; padding-right: 24px; text-align: left; width: 100%;">
                                                E-Mail: <a href="#" class="footer-email"
                                                           style="color: #3C63BC; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left; text-decoration: none;"><?= $userProjectParams->upp_email ?></a><br>
                                                Direct Line: <?= $userProjectParams->upp_phone_number ?> <br>
                                                General line: <?= $project->contactInfo->phone ?>
                                            </th>
                                        </tr>
                                    </table>
                                </th>
                            </tr>
                            <tr style="padding: 0; text-align: left; vertical-align: top;">
                                <th class="footer-bottom"
                                    style="color: #0a0a0a; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-size: 12px; font-weight: normal; line-height: 1.3; margin: 0; padding: 32px 0 45px; text-align: center;">
                                    <table class="row"
                                           style="border-collapse: collapse; border-spacing: 0; display: table; padding: 0; position: relative; text-align: left; vertical-align: top; width: 100%;">
                                        <tr style="padding: 0; text-align: left; vertical-align: top;">
                                            <th class="small-12 large-12 columns first last footer-text text-center"
                                                align="center"
                                                style="color: #333333; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.4em; margin: 0 auto; padding: 0; padding-bottom: 16px; padding-left: 24px; padding-right: 24px; text-align: center; width: 100%;">
                                                <?= $project->name ?> is a part of The
                                                Travel Outlet of Virginia, LLC, major international consolidator for
                                                travel and related services.
                                                <div class="footer-copyright" style="color: #7d7d7d;">
                                                    © 2018 The Travel Outlet of Virginia, LLC. All rights reserved.
                                                </div>
                                            </th>
                                            <th class="expander"
                                                style="color: #0a0a0a; font-family: 'Roboto', Arial, Helvetica, sans-serif; font-weight: normal; line-height: 1.3; margin: 0; padding: 0; text-align: left;"></th>
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
</table>
</body>
