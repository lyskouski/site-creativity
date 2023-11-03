<?php namespace Engine\Request\Page;

/**
 * Description of Basic
 *
 * @author s.lyskovski
 */
class Basic
{

    /**
     *
     * @param string $url
     * @return string
     */
    public function get($url)
    {
        $proxy = \System\Registry::config()->getProxy();

        if ($proxy) {
            $aContext = array(
                'http' => array(
                    'proxy' => \System\Registry::config()->getProxy(),
                    'request_fulluri' => true,
                ),
            );
            $cxContext = stream_context_create($aContext);

            $content = file_get_contents($url, false, $cxContext);
        } else {
            $content = file_get_contents($url);
        }
        return $content;
    }
}
