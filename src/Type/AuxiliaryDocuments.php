<?php

namespace OpenEuropa\EPoetry\Type;

class AuxiliaryDocuments
{

    /**
     * @var \OpenEuropa\EPoetry\Type\AuxiliaryDocument
     */
    private $auxiliaryDocument;

    /**
     * @return \OpenEuropa\EPoetry\Type\AuxiliaryDocument
     */
    public function getAuxiliaryDocument() : \OpenEuropa\EPoetry\Type\AuxiliaryDocument
    {
        return $this->auxiliaryDocument;
    }

    /**
     * @param \OpenEuropa\EPoetry\Type\AuxiliaryDocument $auxiliaryDocument
     * @return $this
     */
    public function setAuxiliaryDocument($auxiliaryDocument) : \OpenEuropa\EPoetry\Type\AuxiliaryDocuments
    {
        $this->auxiliaryDocument = $auxiliaryDocument;
        return $this;
    }


}

