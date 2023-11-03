<?php namespace Engine\Request\Input;

/**
 * File upload
 *
 * @since 2016-12-20
 * @author Viachaslau Lyskouski
 */
class File
{
    /**
     * @var array - FILE attributes
     */
    protected $file;

    public function __construct($name)
    {
        // Undefined | Multiple Files | $_FILES Corruption Attack
        if (
                !isset($_FILES[$name]['error'])
                || is_array($_FILES[$name]['error'])
        ) {
            throw new \Error\Validation('Invalid parameters.');
        }

        // Check $_FILES['upfile']['error'] value.
        switch ($_FILES[$name]['error']) {
            case UPLOAD_ERR_OK:
                break;

            case UPLOAD_ERR_NO_FILE:
                throw new \Error\Validation('No file sent.');

            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new \Error\Validation('Exceeded filesize limit.');

            default:
                throw new \Error\Validation('Unknown errors.');
        }

        $this->file = $_FILES[$name];
    }

    public function getType()
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo->file($this->file['tmp_name']);
    }

    public function getContent()
    {
        return file_get_contents($this->file['tmp_name']);
    }

    public function __destruct()
    {
    //    unlink($this->file['tmp_name']);
    }
}
