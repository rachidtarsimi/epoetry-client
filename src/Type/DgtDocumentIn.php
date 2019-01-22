<?php

namespace OpenEuropa\EPoetry\Type;

class DgtDocumentIn
{

    /**
     * @var string
     */
    private $file;

    /**
     * @var string
     */
    private $format;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @return string
     */
    public function getFile() : string
    {
        return $this->file;
    }

    /**
     * @param string $file
     * @return $this
     */
    public function setFile(string $file) : \OpenEuropa\EPoetry\Type\DgtDocumentIn
    {
        $this->file = $file;
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
     * @param string $format
     * @return $this
     */
    public function setFormat(string $format) : \OpenEuropa\EPoetry\Type\DgtDocumentIn
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType(string $type) : \OpenEuropa\EPoetry\Type\DgtDocumentIn
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name) : \OpenEuropa\EPoetry\Type\DgtDocumentIn
    {
        $this->name = $name;
        return $this;
    }


}

