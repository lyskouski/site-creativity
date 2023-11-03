<?php namespace System\Minify;

/**
 * Shell executions
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package System
 */
class AmpPage
{
    protected $url = '';

    /**
     * Init minifier
     *
     * @param boolean $checkCache
     */
    public function __construct($checkCache = false)
    {
        $url = (new \Engine\Request\Input)->getUrl(null);
        $this->url = (new \Engine\Response\Template)->getUrl($url);
        if ($checkCache) {
            $this->getCache();
        }
    }

    public function getCache()
    {
        if (\System\Registry::user()->isLogged() || !function_exists('apc_exists')) {
            return;
        }
        if (apc_exists($this->url)) {
            $success = false;
            $content = apc_fetch($this->url, $success);
            if ($success) {
                echo "<!DOCTYPE html>\n<html amp>\n", $content, "\n</html>";
                exit;
            }
        }
    }

    public function saveCache($content)
    {
        if (!\System\Registry::user()->isLogged() && function_exists('apc_exists')) {
            apc_add($this->url, $content, \Defines\Database\Params::CACHE_CONTENT);
        }
        return $content;
    }

    /**
     * Content response
     *
     * @param string $content
     * @return string
     */
    public function flush($content)
    {
        $search = array(
            '/.ui{pointer-events:none}/',
            '/<style amp-boilerplate/',
            '/<style/',
            '/!!style!!/',
        //    '/<script/',
            '/<img (.*?) \/>/',
            '/<video /',
            '/<audio /',
            '/action="\//',
            '/\>[^\S ]+/s', // strip whitespaces after tags, except space
            '/[^\S ]+\</s', // strip whitespaces before tags, except space
            '/(\s)+/s' // shorten multiple whitespace sequences
        );

        $replace = array(
            '',
            '!!style!!',
            '<style amp-custom',
            '<style amp-boilerplate',
        //    '<script async',
            '<amp-img layout="responsive" \\1></amp-img>',
            '<amp-video ',
            '<amp-audio ',
            'action="'. \System\Registry::config()->getUrl() . '/',
            '>',
            '<',
            '\\1'
        );

        $res = str_replace(
            array("\n", "\r", "\t"),
            array(),
            preg_replace($search, $replace, $content)
        );

        // Adapt images
        //preg_replace_callback('/<img(.*?)/>/', $callback, $res);


        return $this->saveCache($res);
    }
}
