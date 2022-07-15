<?php

namespace OpenEuropa\EPoetry\Request\Type;

class ProductRequestOut
{
    /**
     * @var string
     */
    private $language;

    /**
     * @var \DateTimeInterface
     */
    private $requestedDeadline;

    /**
     * @var \DateTimeInterface
     */
    private $acceptedDeadline;

    /**
     * @var bool
     */
    private $trackChanges;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $format;

    /**
     * Constructor
     *
     * @var string $language
     * @var \DateTimeInterface $requestedDeadline
     * @var \DateTimeInterface $acceptedDeadline
     * @var bool $trackChanges
     * @var string $status
     * @var string $format
     */
    public function __construct(string $language, \DateTimeInterface $requestedDeadline, \DateTimeInterface $acceptedDeadline, bool $trackChanges, string $status, string $format)
    {
        $this->language = $language;
        $this->requestedDeadline = $requestedDeadline;
        $this->acceptedDeadline = $acceptedDeadline;
        $this->trackChanges = $trackChanges;
        $this->status = $status;
        $this->format = $format;
    }

    /**
     * @param string $language
     * @return $this
     */
    public function setLanguage(string $language) : \OpenEuropa\EPoetry\Request\Type\ProductRequestOut
    {
        $this->language = $language;
        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage() : string
    {
        return $this->language;
    }

    /**
     * @return bool
     */
    public function hasLanguage() : bool
    {
        return !empty($this->language);
    }

    /**
     * @param \DateTimeInterface $requestedDeadline
     * @return $this
     */
    public function setRequestedDeadline(\DateTimeInterface $requestedDeadline) : \OpenEuropa\EPoetry\Request\Type\ProductRequestOut
    {
        $this->requestedDeadline = $requestedDeadline;
        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getRequestedDeadline() : \DateTimeInterface
    {
        return $this->requestedDeadline;
    }

    /**
     * @return bool
     */
    public function hasRequestedDeadline() : bool
    {
        return !empty($this->requestedDeadline);
    }

    /**
     * @param \DateTimeInterface $acceptedDeadline
     * @return $this
     */
    public function setAcceptedDeadline(\DateTimeInterface $acceptedDeadline) : \OpenEuropa\EPoetry\Request\Type\ProductRequestOut
    {
        $this->acceptedDeadline = $acceptedDeadline;
        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getAcceptedDeadline() : \DateTimeInterface
    {
        return $this->acceptedDeadline;
    }

    /**
     * @return bool
     */
    public function hasAcceptedDeadline() : bool
    {
        return !empty($this->acceptedDeadline);
    }

    /**
     * @param bool $trackChanges
     * @return $this
     */
    public function setTrackChanges(bool $trackChanges) : \OpenEuropa\EPoetry\Request\Type\ProductRequestOut
    {
        $this->trackChanges = $trackChanges;
        return $this;
    }

    /**
     * @return bool
     */
    public function isTrackChanges() : bool
    {
        return $this->trackChanges;
    }

    /**
     * @return bool
     */
    public function hasTrackChanges() : bool
    {
        return !empty($this->trackChanges);
    }

    /**
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status) : \OpenEuropa\EPoetry\Request\Type\ProductRequestOut
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus() : string
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function hasStatus() : bool
    {
        return !empty($this->status);
    }

    /**
     * @param string $format
     * @return $this
     */
    public function setFormat(string $format) : \OpenEuropa\EPoetry\Request\Type\ProductRequestOut
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormat() : string
    {
        return $this->format;
    }

    /**
     * @return bool
     */
    public function hasFormat() : bool
    {
        return !empty($this->format);
    }
}

