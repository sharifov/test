<?php
namespace sales\events\lead;


use common\models\LeadPreferences;

/**
 * Class LeadPreferencesUpdateCurrencyEvent
 * @package sales\events\lead
 *
 * @property LeadPreferences $leadPreference
 */
class LeadPreferencesUpdateCurrencyEvent
{
	/**
	 * @var LeadPreferences
	 */
	public $leadPreference;

	/**
	 * LeadPreferencesUpdateCurrencyEvent constructor.
	 * @param LeadPreferences $leadPreference
	 */
	public function __construct(LeadPreferences $leadPreference)
	{
		$this->leadPreference = $leadPreference;
	}
}