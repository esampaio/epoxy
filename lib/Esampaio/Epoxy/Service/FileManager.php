<?php

namespace Glossy\Bundle\CoreBundle\Service;

use Glossy\Bundle\CoreBundle\Entity\FileManager as FileManagerEntity;

/**
 * File Manager service
 */
class FileManager
{
    private $em;
    private $fileUploader;
    private $container;
    private $repository;
    const FILES_FOLDER = 'attachments/';
    const TEMP_FILES_FOLDER = 'tmp/attachments/';

    /**
     * Construct
     *
     * @param \Doctrine\ORM\EntityManager $em           Entity Manager
     * @param object                      $fileUploader File Uploader
     * @param object                      $container    Container
     */
    public function __construct(\Doctrine\ORM\EntityManager $em, $fileUploader, $container)
    {
        $this->em = $em;
        $this->fileUploader = $fileUploader;
        $this->container = $container;
        $this->repository = $container->get('doctrine')->getRepository('GlossyCoreBundle:FileManager');
    }

    /**
     * Get or create by folder
     *
     * @param string $folder Folder
     *
     * @return FileManager
     */
    public function getOrCreateByFolder($folder)
    {
        $entity = $this->repository->findOneByFolder($folder);

        if (!is_object($entity)) {
            $entity = new FileManagerEntity;
            $entity->setFolder($folder);
            $em = $this->container->get('doctrine')->getEntityManager();
            $em->persist($entity);
            $em->flush();
        }

        return $entity;
    }

    /**
     * Retrieves current uploaded files
     *
     * @param object  $object Entity Object
     * @param boolean $temp   Temporary folder? (optional, default false)
     *
     * @return array
     */
    public function getFiles($object, $temp = false)
    {
        if ($temp) {
            return $this->fileUploader->getFiles(array('folder' => self::TEMP_FILES_FOLDER . $this->getId($object)));
        } else {
            $entity = $this->getOrCreateByFolder($this->getId($object));

            return unserialize($entity->getFiles());
        }
    }

    /**
     * Updates current uploaded files on DB
     *
     * @param object $object Entity Object
     */
    public function updateFiles($object)
    {
        $files = serialize($this->fileUploader->getFiles(array('folder' => self::FILES_FOLDER . $this->getId($object))));

        $entity = $this->getOrCreateByFolder($this->getId($object));
        $entity->setFiles($files);

        $this->em->persist($entity);
        $this->em->flush();
    }

    /**
     * Retrieves object upload ID
     *
     * @param object $object Entity Object
     *
     * @return string
     */
    public function getId($object)
    {
        $class = str_replace('\\', '', $this->em->getClassMetadata(get_class($object))->getName());

        return $class . $object->getId();
    }

    /**
     * Sync files to temp
     *
     * @param object $object Entity Object
     */
    public function syncToTemp($object)
    {
        $this->fileUploader->syncFiles(
            array('from_folder'  => self::FILES_FOLDER . $this->getId($object),
            'to_folder'          => self::TEMP_FILES_FOLDER . $this->getId($object),
            'create_to_folder'   => true,
            )
        );
    }

    /**
     * Sync files from temp
     *
     * @param object $object Entity Object
     */
    public function syncFromTemp($object)
    {
        $this->fileUploader->syncFiles(
            array('from_folder'  => self::TEMP_FILES_FOLDER . $this->getId($object),
            'to_folder'          => self::FILES_FOLDER . $this->getId($object),
            'remove_from_folder' => true,
            'create_to_folder'   => true,
            )
        );

        $this->updateFiles($object);
    }

    /**
     * Handle file uploads 
     *
     * @param string $folder Folder to upload to
     */
    public function handleUpload($folder)
    {
        $this->fileUploader->handleFileUpload(array('folder' => self::TEMP_FILES_FOLDER . $folder));
    }
}
