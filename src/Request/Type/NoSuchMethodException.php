<?php

namespace OpenEuropa\EPoetry\Request\Type;

class NoSuchMethodException
{
    /**
     * @var string
     */
    private $message;

    /**
     * Constructor
     *
     * @var string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message) : \OpenEuropa\EPoetry\Request\Type\NoSuchMethodException
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage() : string
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function hasMessage() : bool
    {
        return !empty($this->message);
    }
}

