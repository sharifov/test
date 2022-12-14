<?php

namespace src\model\sms\useCase\send;

use common\models\Client;
use common\models\Employee;

/**
 * Class Contact
 *
 * @property Employee|Client $contact
 */
class Contact
{
    private $contact;

    /**
     * @param Employee|Client $contact
     */
    public function __construct($contact)
    {
        $this->contact = $contact;
    }

    public function getType(): int
    {
        if ($this->isContact()) {
            return Client::TYPE_CONTACT;
        }

        if ($this->isInternal()) {
            return Client::TYPE_INTERNAL;
        }

        throw new \InvalidArgumentException('Undefined contact type');
    }

    public function getId(): int
    {
        if ($this->isContact()) {
            return $this->contact->id;
        }

        if ($this->isInternal()) {
            return $this->contact->id;
        }

        throw new \InvalidArgumentException('Undefined contact type');
    }

    public function getName(): ?string
    {
        if ($this->isContact()) {
            return $this->contact->getNameByType();
        }

        if ($this->isInternal()) {
            return $this->contact->full_name;
        }

        throw new \InvalidArgumentException('Undefined contact type');
    }

    public function getAvatar(): ?string
    {
        if ($this->isContact()) {
            return $this->contact->getAvatar();
        }

        if ($this->isInternal()) {
            return $this->contact->getAvatar();
        }

        throw new \InvalidArgumentException('Undefined contact type');
    }

    public function isContact(): bool
    {
        return $this->contact instanceof Client;
    }

    public function isInternal(): bool
    {
        return $this->contact instanceof Employee;
    }
}
