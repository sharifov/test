<?php

namespace sales\settings;

use Yii;
use yii\base\BaseObject;

/**
 * Class GeneralLineSettings
 * @property  $use_general_line_distribution
 * @property  $general_line_leads_limit
 * @property  $general_line_role_priority
 * @property  $general_line_last_hours
 * @property  $general_line_user_limit
 * @property  $direct_agent_user_limit
 */
class GeneralLineSettings extends BaseObject
{
    public $use_general_line_distribution;
    public $general_line_leads_limit;
    public $general_line_role_priority;
    public $general_line_last_hours;
    public $general_line_user_limit;
    public $direct_agent_user_limit;

    public function __construct($config = [])
    {
        $settings = Yii::$app->params['settings'];
        $general_line_call_distribution = Yii::$app->params['general_line_call_distribution'];

        $this->use_general_line_distribution = $settings['use_general_line_distribution'] ?? $general_line_call_distribution['use_general_line_distribution'];
        $this->general_line_leads_limit = $settings['general_line_leads_limit'] ?? $general_line_call_distribution['general_line_leads_limit'];
        $this->general_line_role_priority = (int)($settings['general_line_role_priority'] ?? $general_line_call_distribution['general_line_role_priority']);
        $this->general_line_last_hours = $settings['general_line_last_hours'] ?? $general_line_call_distribution['general_line_last_hours'];
        $this->general_line_user_limit = $settings['general_line_user_limit'] ?? $general_line_call_distribution['general_line_user_limit'];
        $this->direct_agent_user_limit = $settings['direct_agent_user_limit'] ?? $general_line_call_distribution['direct_agent_user_limit'];

        parent::__construct($config);
    }
}