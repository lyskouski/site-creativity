<?php namespace Modules\Files;

/**
 * Model object for person page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\AbstractModel
{
    /**
     * @var \Data\Doctrine\Main\ContentBlob
     */
    protected $oBlob;

    public function getFile($id)
    {
        $data = explode(',', $this->getBase64($id));
        return array(
            'type' => rtrim(ltrim($data[0], 'data:'), ';base64'),
            'content' => base64_decode($data[1])
        );
    }

    public function getBase64($id)
    {
        $oDbHelper = new \Data\ContentHelper();
        $oBlob = $oDbHelper->getBlob((int) $id);

        $sContent = ',';
        if ($oBlob) {
            $this->oBlob = $oBlob;
            $sContent = $oBlob->getContent();
            if (!is_string($sContent)) {
                $sContent = stream_get_contents($sContent);
            }
        }
        return $sContent;
    }

    public function saveFile($id, $username, $target = '')
    {
        $shareDir = \System\Registry::config()->getPublicPath();
        $relativePath = '/data/' . $username;
        if (!is_dir($shareDir . $relativePath)) {
            mkdir($shareDir . $relativePath);
        }

        $relativePath .= '/images';
        if (!is_dir($shareDir . $relativePath)) {
            mkdir($shareDir . $relativePath);
        }

        $fileData = $this->getFile($id);
        // Define extension by filetype
        $ext = (new \Data\File\Image('', false))->getExtension($fileData['type']);
        $relativePath .= "/{$target}-{$id}.{$ext}";
        $bResult = file_put_contents($shareDir . $relativePath, $fileData['content']);
        // Trigger error if failed
        if (!$bResult) {
            throw new \Error\Validation("Storage permission, file cannot be saved", \Defines\Response\Code::E_FAILED);
        }

        // Remove temporary file
        if ($this->oBlob) {
            $oManager = (new \Data\ContentHelper)->getEntityManager();
            $oManager->remove($this->oBlob);
            $oManager->flush();
        }
        // Get relative path
        return $relativePath;
    }

}
