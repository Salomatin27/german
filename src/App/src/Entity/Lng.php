<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lng
 *
 * @ORM\Table(name="lng")
 * @ORM\Entity
 */
class Lng
{
    /**
     * @var int
     *
     * @ORM\Column(name="lng_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $lngId;

    /**
     * @var string
     *
     * @ORM\Column(name="name_ru", type="string", length=50, nullable=false, options={"comment"="название на русском"})
     */
    private $nameRu;

    /**
     * @var string
     *
     * @ORM\Column(name="name_original", type="string", length=50, nullable=false, options={"comment"="название на оригинальном языке"})
     */
    private $nameOriginal;

    /**
     * @var string
     *
     * @ORM\Column(name="name_short", type="string", length=2, nullable=false, options={"fixed"=true,"comment"="название сокращенное"})
     */
    private $nameShort;

    /**
     * @var bool
     *
     * @ORM\Column(name="lng_default", type="boolean", nullable=false, options={"comment"="язык по умолчанию"})
     */
    private $lngDefault = '0';



    /**
     * Get lngId.
     *
     * @return int
     */
    public function getLngId()
    {
        return $this->lngId;
    }

    /**
     * Set nameRu.
     *
     * @param string $nameRu
     *
     * @return Lng
     */
    public function setNameRu($nameRu)
    {
        $this->nameRu = $nameRu;

        return $this;
    }

    /**
     * Get nameRu.
     *
     * @return string
     */
    public function getNameRu()
    {
        return $this->nameRu;
    }

    /**
     * Set nameOriginal.
     *
     * @param string $nameOriginal
     *
     * @return Lng
     */
    public function setNameOriginal($nameOriginal)
    {
        $this->nameOriginal = $nameOriginal;

        return $this;
    }

    /**
     * Get nameOriginal.
     *
     * @return string
     */
    public function getNameOriginal()
    {
        return $this->nameOriginal;
    }

    /**
     * Set nameShort.
     *
     * @param string $nameShort
     *
     * @return Lng
     */
    public function setNameShort($nameShort)
    {
        $this->nameShort = $nameShort;

        return $this;
    }

    /**
     * Get nameShort.
     *
     * @return string
     */
    public function getNameShort()
    {
        return $this->nameShort;
    }

    /**
     * Set lngDefault.
     *
     * @param bool $lngDefault
     *
     * @return Lng
     */
    public function setLngDefault($lngDefault)
    {
        $this->lngDefault = $lngDefault;

        return $this;
    }

    /**
     * Get lngDefault.
     *
     * @return bool
     */
    public function getLngDefault()
    {
        return $this->lngDefault;
    }
}
