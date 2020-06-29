<?php

namespace sales\forms\api\clientEmail;


use sales\repositories\emailUnsubscribe\EmailUnsubscribeRepository;
use yii\base\Model;

/**
 * Class SubscribeForm
 * @property string $email
 * @property int $project_id
 */
class SubscribeForm extends Model
{
    public string $email;
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

            ['email', function ($attribute) {
                if (!(new EmailUnsubscribeRepository())->find($this->email, $this->project_id)) {
                    $this->addError($attribute,
                    'Database entry (email : ' . $this->email . ', project : ' . $this->project_id . ') not exists');
                }
            }],
        ];
    }
}
