<?php

declare(strict_types = 1);

namespace OpenEuropa\EPoetry\Type;

class LinguisticSections
{
    /**
     * @var \OpenEuropa\EPoetry\Type\LinguisticSection[]
     */
    private $linguisticSection;

    /**
     * @param \OpenEuropa\EPoetry\Type\LinguisticSection $linguisticSection
     *
     * @return $this
     */
    public function addLinguisticSection(LinguisticSection $linguisticSection): self
    {
        $this->linguisticSection = \is_array($this->linguisticSection) ? $this->linguisticSection : [];
        $this->linguisticSection[] = $linguisticSection;

        return $this;
    }

    /**
     * @return \OpenEuropa\EPoetry\Type\LinguisticSection[]
     */
    public function getLinguisticSection(): array
    {
        return $this->linguisticSection;
    }

    /**
     * @param \OpenEuropa\EPoetry\Type\LinguisticSection $linguisticSection
     *
     * @return $this
     */
    public function setLinguisticSection($linguisticSection): self
    {
        $this->linguisticSection = $linguisticSection;

        return $this;
    }
}
