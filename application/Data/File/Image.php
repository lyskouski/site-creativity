<?php namespace Data\File;

/**
 * Image manipulation
 *
 * @since 2016-09-30
 * @author Viachaslau Lyskouski
 */
class Image
{
    private $isBlob = false;
    private $content = '';

    private $fileType = array(
        'image/png' => 'png',
        'image/jpeg' => 'jpg',
        'image/gif' => 'gif'
    );

    public function getExtension($fileType)
    {
        if (!array_key_exists($fileType, $this->fileType)) {
            throw new \Error\Validation(
                'Image Content-Type: ' . filter_var($fileType, FILTER_SANITIZE_STRING),
                \Defines\Response\Code::E_FORBIDDEN
            );
        }
        return $this->fileType[$fileType];
    }

    /**
     * Init image
     * @param string $content
     */
    public function __construct($content, $err = true)
    {
        $url = filter_var($content, FILTER_SANITIZE_URL);

        if ($content && $content[0] === '/') {
            // ingore any changes

        // Check blob input
        } elseif ($this->isBase64($content)) {
            $this->isBlob = true;

        // Grab from URL
        } elseif ($url) {
            if (strpos($url, \System\Registry::config()->getUrl(null, false)) === false) {
                list($type, $blob) = $this->captureImage($url);
                $content = "data:$type;base64,$blob";
                $this->isBlob = true;
            }

        // Not applicable content
        } elseif ($err) {
            $err = \Defines\Response\Code::E_NOT_ALLOWED;
            throw new \Error\Validation(\Defines\Response\Code::getHeader($err), $err);
        }
        $this->content = $content;
    }

    private function isBase64($content)
    {
        $isData = false;
        $pattern = 'data:%s;base64,';
        foreach (array_keys($this->fileType) as $type) {
            if (strpos($content, sprintf($pattern, $type)) === 0) {
                $isData = true;
                $content = substr($content, strlen($pattern . $type) - 2);
                break;
            }
        }
        if ($isData) {
            $check = new \Engine\Validate\Form();
            $isData = $check->isBase64($content);
        }
        return $isData;
    }

    private function captureImage($url)
    {
        $walker = new \Engine\Request\Page\Walker();

        // Visit page just in case of some extra-permissions
        $a = parse_url($url);
        $walker->getContent($a['scheme'] . '://' . $a['host']);
        $walker->setHeader(null, 'Host: ' . $a['host'], true, true);

        $content = $walker->getContent($url);
        if ($walker->getHeader('location')) {
            $content = $walker->getContent($walker->getHeader('location'));
        }

        $blob = base64_encode($content);
        $type = $walker->getHeader('content type');
        // validate content type
        $this->getExtension($type);
        return array($type, $blob);
    }

    public function isBlob()
    {
        return $this->isBlob;
    }

    public function getContent()
    {
        return $this->content;
    }

}
