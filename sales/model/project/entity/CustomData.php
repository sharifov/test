<?php

namespace sales\model\project\entity;

/**
 * Class CustomData
 *
 * @property $background_color
 * @property $color
 * @property $url_say_play_hold
 * @property $url_music_play_hold
 * @property $play_direct_message
 * @property $play_redirect_message
 * @property $say_direct_message
 * @property $say_redirect_message
 * @property $sms_enabled
 * @property ObjectData $object
 */
class CustomData
{
    public $background_color;
    public $color;
    public $url_say_play_hold;
    public $url_music_play_hold;
    public $play_direct_message;
    public $play_redirect_message;
    public $say_direct_message;
    public $say_redirect_message;
    public $sms_enabled;
    public ObjectData $object;

    public function __construct(?string $raw, ?int $projectId)
    {
        if (!$raw) {
            return;
        }
        try {
            $customData = @json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            $this->background_color = $customData['background-color'] ?? null;
            $this->color = $customData['color'] ?? null;
            $this->url_say_play_hold = $customData['url_say_play_hold'] ?? null;
            $this->url_music_play_hold = $customData['url_music_play_hold'] ?? null;
            $this->play_direct_message = $customData['play_direct_message'] ?? null;
            $this->play_redirect_message = $customData['play_redirect_message'] ?? null;
            $this->say_direct_message = $customData['say_direct_message'] ?? null;
            $this->say_redirect_message = $customData['say_redirect_message'] ?? null;
            $this->sms_enabled = $customData['sms_enabled'] ?? null;
            $this->object = new ObjectData($customData['object'] ?? []);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Project custom data error',
                'projectId' => $projectId,
                'raw' => $raw,
                'error' => $e->getMessage(),
            ], 'project\entity\CustomData\constructor');
        }
    }
}
