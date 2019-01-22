<?php

declare(strict_types = 1);

namespace OpenEuropa\EPoetry\Type;

class LinguisticSectionIn
{
    /**
     * @var \OpenEuropa\EPoetry\Type\LanguageIn
     */
    private $language;

    /**
     * @return \OpenEuropa\EPoetry\Type\LanguageIn
     */
    public function getLanguage(): LanguageIn
    {
        return $this->language;
    }

    /**
     * @param \OpenEuropa\EPoetry\Type\LanguageIn $language
     *
     * @return $this
     */
    public function setLanguage($language): self
    {
        $this->language = $language;

        return $this;
    }
}
