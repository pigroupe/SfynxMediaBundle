<?php

namespace Sfynx\MediaBundle\Layers\Domain\Service\Zip;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Filesystem;

use Sfynx\MediaBundle\Layers\Domain\Service\Mediatheque\Manager\EntityManager;

use \ZipArchive;

/**
 * Api provider class to upload files
 *
 * @category Sfynx\MediaBundle\Layers
 * @package Domain
 * @subpackage Service\Zip
 * @author Etienne de Longeaux <etienne.delongeaux@gmail.com>
 */
class ZipDownload
{
    /** @var EntityManager */
    protected $mediaManager;
    /** @var array */
    protected $cacheDirs;
    /** @var array|null */
    protected $options;

    /**
     * ZipDownload constructor.
     *
     * @param EntityManager $mediaManager
     * @param string $cacheDirs
     * @param array|null $options
     */
    public function __construct(
        EntityManager $mediaManager,
        $cacheDirs,
        array $options = null
    ) {
        $this->mediaManager = $mediaManager;
        $this->cacheDirs = $cacheDirs;
        $this->options = $options;
    }

    /**
     * Gestion des fichiers simples, fichiers avec parents et le retour de la reponse
     *
     * @param string|array $listMediaIdsToZip   Liste des id des documents/medias à supprimer
     * @param array        $options             Contient les préfixes des noms du zip et du cache
     *                                          (peut contenir d'autres paramètres ci-besoin)
     * @return             Response
     */
    public function zipDownloadMedia($listMediaIdsToZip, $options)
    {
        if (\is_string($listMediaIdsToZip)) {
            $arrayMediasId = \explode(",",$listMediaIdsToZip);
        } else {
            $arrayMediasId = $listMediaIdsToZip;
        }

        $medias = $arrayAllChildren = $arraySingleMedia = $urlsSingleMedia = $urlsChildren = [];
        foreach ($arrayMediasId as $mediaId) {
            $medias[] = $this->mediaManager->find($mediaId);
        }
        foreach ($medias as $media) {
            if (\count($media->getChildrenInfos()) == 0) {
                \array_push($arraySingleMedia, $media);
            }
        }
        foreach ($arraySingleMedia as $media) {
            $key = $media->getImage()->getId() . '_' . $media->getImage()->getName() . '.' . $media->getImage()->getExtension();
            $urlsSingleMedia[$key] = $media->getMedia()->getpublicUri().'.'.$media->getMedia()->getExtension();
        }

        if (\method_exists($this->mediaManager, 'findChildrensByParents')) {
            $arrayAllChildren = $this->mediaManager->findChildrensByParents($medias);
            foreach ($arrayAllChildren as $media) {
                $key = $media->getImage()->getId() . '_' . $media->getImage()->getName() . '.' . $media->getImage()->getExtension();
                $urlsChildren[$key] = $media->getMedia()->getpublicUri() . '.' . $media->getMedia()->getExtension();
            }
        }

        $urls = \array_merge($urlsSingleMedia, $urlsChildren);

        $zipRootCacheDir = $this->cacheDirs['zip'][$options['zip-cache-name']];
        $zipName = $options['zip-name'].".zip";

        $fileSystem = new Filesystem();

        $pathZipRootCacheDir = self::create($urls, $zipName, $fileSystem, $zipRootCacheDir);

        $response = new Response(\file_get_contents($pathZipRootCacheDir));
        $response->headers->set('Content-Type','application/zip');
        $response->headers->set('Content-Disposition','attachment;filename="' . $zipName . '"');
        $response->headers->set('Content-length', filesize($pathZipRootCacheDir));

        //delete file from zip/gallery
        $fileSystem->remove([$pathZipRootCacheDir]);

        return $response;
    }

    /**
     * Création du zip
     *
     * @param array      $urls              ulrs des fichiers à zipper
     * @param string     $zipName           nom du zip après compression
     * @param FileSystem $fileSystem        Utilitaire de manipulation de fichier
     * @param string     $zipRootCacheDir   chemin de stockage du zip en cache
     * @return string
     */
    public static function create($urls, $zipName, $fileSystem, $zipRootCacheDir)
    {
        $fileSystem->mkdir($zipRootCacheDir);
        $pathZipRootCacheDir = $zipRootCacheDir.$zipName;

        $zip = new ZipArchive();
        $zip->open($pathZipRootCacheDir, ZIPARCHIVE::CREATE);

        foreach ($urls as $key => $f) {
            if ($stream = fopen($f, 'r')) {
                // affiche toute la page, en commençant à la position 10
                $stream_content = stream_get_contents($stream);
                fclose($stream);
            }
            $zip->addFromString($key, $stream_content);
        }
        $zip->close();

        return $pathZipRootCacheDir;
    }
}

