<?php

namespace common\components;

use Twilio\Rest\Api\V2010\Account\AuthorizedConnectAppInstance;
use Twilio\Rest\Api\V2010\Account\BalanceInstance;
use Twilio\Rest\Api\V2010\Account\IncomingPhoneNumberInstance;
use yii\base\Component;
use Twilio\Rest\Client;


/**
 * This is the TwilioClient class
 *
 * @property string $account_sid
 * @property string $auth_token
 * @property string $app_sid
 * @property string $twilio_phone_number
 * @property string $sip_domain_sid
 *
 * @property string $messagingStatusCallbackUri
 * @property string $messagingFallbackUri
 * @property string $messagingRequestUri
 *
 * @property string $voiceStatusCallbackUri
 * @property string $voiceFallbackUri
 * @property string $voiceRequestUri
 *
 * @property Client $client
 *
 * RegEx Matching for E.164
 * regular expression: ^\+?[1-9]\d{1,14}$
 *
 *
 */

class TwilioClient extends Component
{

	// Message Status Values - https://www.twilio.com/docs/sms/api/message-resource#message-status-values

	public const STATUS_QUEUED          = 'queued';
	public const STATUS_SENT            = 'sent';
	public const STATUS_DELIVERED       = 'delivered';
	public const STATUS_UNDELIVERED     = 'undelivered';
	public const STATUS_FAILED          = 'failed';

	public const STATUS_ACCEPTED        = 'accepted';
	public const STATUS_SENDING         = 'sending';
	public const STATUS_RECEIVING       = 'receiving';
	public const STATUS_RECEIVED        = 'received';



	public const STATUS_VOICE_QUEUED        = 'queued';
	public const STATUS_VOICE_RINGING       = 'ringing';
	public const STATUS_VOICE_IN_PROGRESS   = 'in-progress';
	public const STATUS_VOICE_COMPLETED     = 'completed';
	public const STATUS_VOICE_BUSY          = 'busy';
	public const STATUS_VOICE_FAILED        = 'failed';
	public const STATUS_VOICE_NO_ANSWER     = 'no-answer';

	/*
	 * STATUS	DESCRIPTION
		queued	The call is ready and waiting in line before going out.
		ringing	The call is currently ringing.
		in-progress	The call was answered and is actively in progress.
		completed	The call was answered and has ended normally.
		busy	The caller received a busy signal.
		failed	The call could not be completed as dialed, most likely because the phone number was non-existent.
		no-answer	The call ended without being answered.
		canceled	The call was canceled via the REST API while queued or ringing.
	 */


	public const DIRECTION_INBOUND          = 'inbound';
	public const DIRECTION_OUTBOUND_API     = 'outbound-api';
	public const DIRECTION_OUTBOUND_DIAL    = 'outbound-dial';



	public $account_sid;
	public $auth_token;
	public $app_sid;
	public $sip_domain_sid;

	public $client;

	public $messagingStatusCallbackUri;
	public $messagingFallbackUri;
	public $messagingRequestUri;

	public $voiceStatusCallbackUri;
	public $voiceFallbackUri;
	public $voiceRequestUri;


	/**
	 * @return \Twilio\Rest\Client|void
	 * @throws \Twilio\Exceptions\ConfigurationException
	 */
	public function init()
	{
		parent::init();
		$twilio = new Client($this->account_sid, $this->auth_token);
		$this->client = $twilio;
	}


	public function accountInfo(): AuthorizedConnectAppInstance
	{
		return $this->client->authorizedConnectApps($this->app_sid)->fetch();
	}

	public function sipInfo(): \Twilio\Rest\Api\V2010\Account\Sip\DomainInstance
	{
		return $this->client->sip->domains($this->sip_domain_sid)->fetch();
	}

	/**
	 * @return IncomingPhoneNumberInstance[]
	 */
	public function incomingPhoneNumbers(): array
	{
		return $this->client->incomingPhoneNumbers->read();
	}

	public function getBalance(): BalanceInstance
	{
		return $this->client->api->account->balance->fetch();
	}

}