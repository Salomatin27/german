<?php
namespace Patient\Service;

use App\Entity\Image;
use App\Entity\Operation;
use App\Entity\Patient;
use Doctrine\ORM\EntityManager;

class ImageService
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getImages($id)
    {
        $sql = 'select image_id as id, image_name as name from image where operation_id=:id';
        $query = $this->entityManager->getConnection()->prepare($sql);
        $query->bindValue('id', $id);
        $query->execute();
        $images = $query->fetchAll();

        return $images;
    }

    public function setImage($data, $id)
    {
        $file=$data['file']['tmp_name'];
        /** @var Operation $object */
        $object = $this->entityManager->getRepository(Operation::class)->find($id);
        $image = new Image();
        $image->setOperation($object);
        $image->setImageName($data['file']['name']);
        $type = pathinfo($data['file']['name'], PATHINFO_EXTENSION);

        $file_thumbnail=$this->resizeImage($file, $type);
        $content_thumbnail=$this->getImageFileContent($file_thumbnail);
        $fileInfo=$this->getImageFileInfo($file_thumbnail);
        $image->setThSize($fileInfo['size']);
        $image->setThType($fileInfo['type']);
        $image->setThumbnail($content_thumbnail);
        unlink($file_thumbnail);

        $file_present=$this->resizeImage($file, $type, 900);
        $content_present=$this->getImageFileContent($file_present);
        $image->setImage($content_present);
        $fileInfo=$this->getImageFileInfo($file_present);
        $image->setImageSize($fileInfo['size']);
        $image->setImageType($fileInfo['type']);
        unlink($file_present);

        $this->entityManager->persist($image);
        $this->entityManager->flush();

        unlink($file);

        $id = $image->getImageId();

        return ['id' => $id, 'name' => $image->getImageName()];
    }

    public function getImageFileInfo($filePath)
    {
        // Пробуем открыть файл
        if (!is_readable($filePath)) {
            return false;
        }

        // Получаем размер файла в байтах.
        $fileSize = filesize($filePath);

        // Получаем MIME-тип файла.
        $f_info = finfo_open(FILEINFO_MIME);
        $mimeType = finfo_file($f_info, $filePath);
        if ($mimeType === false) {
            $mimeType = 'application/octet-stream';
        }

        return [
            'size' => $fileSize,
            'type' => $mimeType
        ];
    }

    public function getImageFileContent($filePath)
    {
        return file_get_contents($filePath);
    }

    /**
     * original: desireWidth=240->640x480(240x180), changed 19.09.2019 to 1200x900(240x180)
     * @param $filePath
     * @param $type
     * @param int $desiredHeight
     * @return bool|string
     */
    public function resizeImage($filePath, $type, $desiredHeight = 180)
    {
        $type = strtolower($type);

        // Получаем исходную размерность файла.
        list($originalWidth, $originalHeight) = getimagesize($filePath);

        // Вычисляем соотношение сторон.
        $aspectRatio = $originalWidth/$originalHeight;
        // Вычисляем получившуюся высоту.
        $desiredWidth = $desiredHeight*$aspectRatio;

        // Изменяем размер изображения.
        $resultingImage = imagecreatetruecolor($desiredWidth, $desiredHeight);
        switch ($type) {
            case 'jpeg':
            case 'jpg':
                $originalImage = imagecreatefromjpeg($filePath);
                break;
            case 'png':
                $originalImage = imagecreatefrompng($filePath);
                break;
            case 'gif':
                $originalImage = imagecreatefromgif($filePath);
                break;
            default:
                return false;
        }
        imagecopyresampled(
            $resultingImage,
            $originalImage,
            0,
            0,
            0,
            0,
            $desiredWidth,
            $desiredHeight,
            $originalWidth,
            $originalHeight
        );

        // Сохраняем измененное изображение во временное хранилище.
        $tmpFileName = tempnam("/tmp", "FOO");
//        imagepng($resultingImage, $tmpFileName, 6);
        imagejpeg($resultingImage, $tmpFileName, 75);

        // Возвращаем путь к получившемуся изображению.
        return $tmpFileName;
    }

    public function getImage($id, $isThumbnail)
    {
        /** @var Image $image */
        $image = $this->entityManager->getRepository(Image::class)
            ->find($id);

        if ($isThumbnail) {
            $fileContent = $image->getThumbnail();
            $fileType=$image->getThType();
            $fileSize=$image->getThSize();
        } else {
            $fileContent = $image->getImage();
            $fileType=$image->getImageType();
            $fileSize=$image->getImageSize();
        }


        return $file = [
            'content' => $fileContent,
            'type'    => $fileType,
            'size'    => $fileSize,
        ];
    }

    public function deleteImage($id)
    {
        $image = $this->entityManager->getRepository(Image::class)
            ->find($id);

        if ($image === null) {
            return 'not found';
        }
        try {
            $this->entityManager->remove($image);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }

        return 'deleted';
    }

    public function setPatientPhoto(array $data, $id)
    {
        $file=$data['file']['tmp_name'];
        /** @var Patient $object */
        $object = $this->entityManager->getRepository(Patient::class)->find($id);
        $object->setImageName($data['file']['name']);
        $type = pathinfo($data['file']['name'], PATHINFO_EXTENSION);

        $file_present=$this->resizeImage($file, $type);
        $content_present=$this->getImageFileContent($file_present);
        $object->setImage($content_present);
        $fileInfo=$this->getImageFileInfo($file_present);
        $object->setImageSize($fileInfo['size']);
        $object->setImageType($fileInfo['type']);
        unlink($file_present);

        $this->entityManager->persist($object);
        $this->entityManager->flush($object);

        unlink($file);
    }

}
