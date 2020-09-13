<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Image
 *
 * @ORM\Table(name="image", indexes={@ORM\Index(name="image_operation_operation_id_fk", columns={"operation_id"})})
 * @ORM\Entity
 */
class Image
{
    /**
     * @var int
     *
     * @ORM\Column(name="image_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $imageId;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="blob", length=16777215, nullable=false, options={"comment"="изображение"})
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="image_name", type="string", length=50, nullable=false, options={"comment"="имя изображения"})
     */
    private $imageName;

    /**
     * @var string
     *
     * @ORM\Column(name="image_size", type="string", length=50, nullable=false, options={"comment"="размер изображения"})
     */
    private $imageSize;

    /**
     * @var string
     *
     * @ORM\Column(name="image_type", type="string", length=100, nullable=false, options={"comment"="тип изображения"})
     */
    private $imageType;

    /**
     * @var string
     *
     * @ORM\Column(name="thumbnail", type="blob", length=16777215, nullable=false)
     */
    private $thumbnail;

    /**
     * @var string
     *
     * @ORM\Column(name="th_size", type="string", length=50, nullable=false)
     */
    private $thSize;

    /**
     * @var string
     *
     * @ORM\Column(name="th_type", type="string", length=100, nullable=false)
     */
    private $thType;

    /**
     * @var \App\Entity\Operation
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Operation")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="operation_id", referencedColumnName="operation_id")
     * })
     */
    private $operation;



    /**
     * Get imageId.
     *
     * @return int
     */
    public function getImageId()
    {
        return $this->imageId;
    }

    /**
     * Set image.
     *
     * @param string $image
     *
     * @return Image
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set imageName.
     *
     * @param string $imageName
     *
     * @return Image
     */
    public function setImageName($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

    /**
     * Get imageName.
     *
     * @return string
     */
    public function getImageName()
    {
        return $this->imageName;
    }

    /**
     * Set imageSize.
     *
     * @param string $imageSize
     *
     * @return Image
     */
    public function setImageSize($imageSize)
    {
        $this->imageSize = $imageSize;

        return $this;
    }

    /**
     * Get imageSize.
     *
     * @return string
     */
    public function getImageSize()
    {
        return $this->imageSize;
    }

    /**
     * Set imageType.
     *
     * @param string $imageType
     *
     * @return Image
     */
    public function setImageType($imageType)
    {
        $this->imageType = $imageType;

        return $this;
    }

    /**
     * Get imageType.
     *
     * @return string
     */
    public function getImageType()
    {
        return $this->imageType;
    }

    /**
     * Set thumbnail.
     *
     * @param string $thumbnail
     *
     * @return Image
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Get thumbnail.
     *
     * @return string
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Set thSize.
     *
     * @param string $thSize
     *
     * @return Image
     */
    public function setThSize($thSize)
    {
        $this->thSize = $thSize;

        return $this;
    }

    /**
     * Get thSize.
     *
     * @return string
     */
    public function getThSize()
    {
        return $this->thSize;
    }

    /**
     * Set thType.
     *
     * @param string $thType
     *
     * @return Image
     */
    public function setThType($thType)
    {
        $this->thType = $thType;

        return $this;
    }

    /**
     * Get thType.
     *
     * @return string
     */
    public function getThType()
    {
        return $this->thType;
    }


    /**
     * Set operation.
     *
     * @param \App\Entity\Operation|null $operation
     *
     * @return Image
     */
    public function setOperation(\App\Entity\Operation $operation = null)
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * Get operation.
     *
     * @return \App\Entity\Operation|null
     */
    public function getOperation()
    {
        return $this->operation;
    }
}
