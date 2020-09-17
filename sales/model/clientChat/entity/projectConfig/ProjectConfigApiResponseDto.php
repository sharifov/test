<?php
namespace sales\model\clientChat\entity\projectConfig;

use sales\model\clientChat\entity\channelTranslate\ClientChatChannelTranslate;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use yii\helpers\Json;

/**
 * Class ProjectConfigApiResponse
 * @package sales\model\clientChat\entity\projectConfig
 *
 * @property string $endpoint
 * @property bool $enabled
 * @property string $project
 * @property string $projectKey
 * @property string $notificationSound
 * @property array $theme
 * @property array $registration
 * @property array $settings
 * @property array $channels
 */
class ProjectConfigApiResponseDto
{
	/**
	 * @var string $endpoint
	 */
	public string $endpoint;

	/**
	 * @var bool $enabled
	 */
	public bool $enabled;

	/**
	 * @var string $project
	 */
	public string $project;

    /**
     * @var string $projectKey
     */
    public string $projectKey;

	/**
	 * @var string $notificationSound
	 */
	public string $notificationSound;

	/**
	 * @var array $theme
	 */
	public array $theme;

	/**
	 * @var array $registration
	 */
	public array $registration;

	/**
	 * @var array $settings
	 */
	public array $settings;

    /**
     * @var array $channels
     */
    public array $channels;

    /**
     * ProjectConfigApiResponseDto constructor.
     * @param ClientChatProjectConfig $projectConfig
     * @param string|null $languageId
     */
	public function __construct(ClientChatProjectConfig $projectConfig, ?string $languageId = null)
	{
		$params = Json::decode($projectConfig->ccpc_params_json);

		$this->endpoint = $params['endpoint'] ?? '';
		$this->notificationSound = $params['notificationSound'] ?? '';
		$this->enabled = (bool)$projectConfig->ccpc_enabled;
		$this->project = $projectConfig->ccpcProject ? $projectConfig->ccpcProject->name : '';
        $this->projectKey = $projectConfig->ccpcProject ? $projectConfig->ccpcProject->project_key : '';
		$this->theme = Json::decode($projectConfig->ccpc_theme_json) ?? '{}';
		$this->registration = Json::decode($projectConfig->ccpc_registration_json) ?? '{}';
		$this->settings = Json::decode($projectConfig->ccpc_settings_json) ?? '{}';
        $this->channels = ClientChatChannel::getSettingsList($projectConfig->ccpc_project_id, $languageId);
	}
}