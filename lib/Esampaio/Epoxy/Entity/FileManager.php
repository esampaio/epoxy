<?php

namespace Glossy\Bundle\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Glossy\Bundle\CoreBundle\Entity\FileManager
 *
 * @ORM\Table(name="file_manager")
 * @ORM\Entity()
 */
class FileManager
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $folder
     *
     * @ORM\Column(name="folder", type="string", length=255, nullable=false)
     */
    private $folder;

    /**
     * @var string $files
     *
     * @ORM\Column(name="files", type="text", nullable=true)
     */
    private $files;

    /**
     * Set folder
     *
     * @param string $folder
     *
     * @return FileManager
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get folder
     *
     * @return string
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * Set files
     *
     * @param string $files
     *
     * @return FileManager
     */
    public function setFiles($files)
    {
        $this->files = $files;

        return $this;
    }

    /**
     * Get files
     *
     * @return string
     */
    public function getFiles()
    {
        return $this->files;
    }
}
