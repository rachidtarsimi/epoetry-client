<?php

declare(strict_types = 1);

namespace OpenEuropa\EPoetry\Type;

class Contacts
{
    /**
     * @var null|\OpenEuropa\EPoetry\Type\ContactPerson[]
     */
    protected $contact;

    /**
     * @param ContactPerson ...$contacts
     *
     * @return $this
     */
    public function addContact(...$contacts): Contacts
    {
        foreach ($contacts as $contact) {
            $this->contact[] = $contact;
        }

        return $this;
    }

    /**
     * @return null|\OpenEuropa\EPoetry\Type\ContactPerson[]
     */
    public function getContact(): ?array
    {
        return $this->contact;
    }

    /**
     * @return bool
     */
    public function hasContact(): bool
    {
        if (\is_array($this->contact)) {
            return !empty($this->contact);
        }

        return isset($this->contact);
    }

    /**
     * @param ContactPerson[] $contact
     *
     * @return $this
     */
    public function setContact(array $contact): Contacts
    {
        $this->contact = $contact;

        return $this;
    }
}
