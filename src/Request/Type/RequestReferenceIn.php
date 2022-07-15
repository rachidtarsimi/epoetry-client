<?php

namespace OpenEuropa\EPoetry\Request\Type;

class RequestReferenceIn
{
    /**
     * @var null|\OpenEuropa\EPoetry\Request\Type\DossierReference
     */
    private $dossier;

    /**
     * @var null|string
     */
    private $productType;

    /**
     * @var null|int
     */
    private $part;

    /**
     * Constructor
     *
     * @var \OpenEuropa\EPoetry\Request\Type\DossierReference $dossier
     * @var string $productType
     * @var int $part
     */
    public function __construct(\OpenEuropa\EPoetry\Request\Type\DossierReference $dossier, string $productType, int $part)
    {
        $this->dossier = $dossier;
        $this->productType = $productType;
        $this->part = $part;
    }

    /**
     * @param \OpenEuropa\EPoetry\Request\Type\DossierReference $dossier
     * @return $this
     */
    public function setDossier($dossier) : \OpenEuropa\EPoetry\Request\Type\RequestReferenceIn
    {
        $this->dossier = $dossier;
        return $this;
    }

    /**
     * @return \OpenEuropa\EPoetry\Request\Type\DossierReference|null
     */
    public function getDossier() : ?\OpenEuropa\EPoetry\Request\Type\DossierReference
    {
        return $this->dossier;
    }

    /**
     * @return bool
     */
    public function hasDossier() : bool
    {
        return !empty($this->dossier);
    }

    /**
     * @param string $productType
     * @return $this
     */
    public function setProductType(string $productType) : \OpenEuropa\EPoetry\Request\Type\RequestReferenceIn
    {
        $this->productType = $productType;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getProductType() : ?string
    {
        return $this->productType;
    }

    /**
     * @return bool
     */
    public function hasProductType() : bool
    {
        return !empty($this->productType);
    }

    /**
     * @param int $part
     * @return $this
     */
    public function setPart(int $part) : \OpenEuropa\EPoetry\Request\Type\RequestReferenceIn
    {
        $this->part = $part;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPart() : ?int
    {
        return $this->part;
    }

    /**
     * @return bool
     */
    public function hasPart() : bool
    {
        return !empty($this->part);
    }
}

