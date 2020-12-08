<?php

namespace sales\forms\api\clientEmail;

use sales\repositories\emailUnsubscribe\EmailUnsubscribeRepository;
use yii\base\Model;

/**
 * Class UnSubscribeForm
 * @property string $email
 * @property int $project_id
 */
class UnSubscribeForm extends Model
{
    public $email;
    public int $project_id;

    /**
     * SubscribeForm constructor.
     * @param int $project_id
     * @param array $config
     */
    public function __construct(int $project_id, $config = [])
    {
        parent::__construct($config);
        $this->project_id = $project_id;
    }

    public function formName(): string
    {
        return '';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 160],
        ];
    }
}
