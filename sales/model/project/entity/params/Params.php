<?php

namespace sales\model\project\entity\params;

/**
 * Class Params
 *
 * @property StyleParams $style
 * @property ObjectParams $object
 * @property CallParams $call
 * @property SmsParams $sms
 * @property Webhook $webhook
 * @property ClientNotification $clientNotification
 */
class Params
{
    public StyleParams $style;
    public ObjectParams $object;
    public CallParams $call;
    public SmsParams $sms;
    public Webhook $webhook;
    public ClientNotification $clientNotification;

    private function __construct(array $params)
    {
        $this->style = new StyleParams($params['style'] ?? []);
        $this->object = new ObjectParams($params['object'] ?? []);
        $this->call = new CallParams($params['call'] ?? []);
        $this->sms = new SmsParams($params['sms'] ?? []);
        $this->webhook = new Webhook($params['webhook'] ?? []);
        $this->clientNotification = new ClientNotification($params['clientNotification'] ?? []);
    }

    public static function fromArray(array $params): self
    {
        return new static($params);
    }

    public static function fromJson(?string $json, ?int $projectId): self
    {
        $params = [];
        try {
            if ($json) {
                $params = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
            }
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Project params error',
                'projectId' => $projectId,
                'json' => $json,
                'error' => $e->getMessage(),
            ], 'project\entity\params\Params\constructor');
            $params = [];
        }
        return self::fromArray($params);
    }

    public static function default(): array
    {
        return [
            'style' => StyleParams::default(),
            'object' => ObjectParams::default(),
            'call' => CallParams::default(),
            'sms' => SmsParams::default(),
            'webhook' => Webhook::default(),
            'clientNotification' => new ClientNotification([])
        ];
    }
}
