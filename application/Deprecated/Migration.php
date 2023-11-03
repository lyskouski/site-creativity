<?php namespace Deprecated;

/**
 * Backward compatibility with previous versions
 *
 * @since 2016-10-11
 * @author Viachaslau Lyskouski
 * @package Deprecated
 */
class Migration
{

    public function redirect($url, $code = '301 Moved Permanently')
    {
        header("HTTP/1.1 $code");
        header("Location: $url");
        exit;
    }

    /**
     * Fix search errors
     * @note _escaped_fragment_
     * @note /ru/ru
     */
    public function checkSearch()
    {
        $oInput = new \Engine\Request\Input();
        $extra = $oInput->getServer('REQUEST_URI', '', FILTER_DEFAULT);

        $template = new \Engine\Response\Template();
        $check = rtrim(ltrim($template->getUrl($extra, '.', '/'), '/'), '.');

        // Get URL
        $siteUrl = $oInput->getUrlProtocol() . '://' . $oInput->getServer('HTTP_HOST');
        if (!in_array($siteUrl, \System\Registry::config()->getUrlList())) {
            $siteUrl = \System\Registry::config()->getUrl(null, false);
        }

        if (strpos($extra, '?_escaped_fragment_=') !== false || array_key_exists('_escaped_fragment_', $oInput->getGet())) {
            $this->redirect($siteUrl . str_replace('?_escaped_fragment_=', '', $oInput->getServer('REQUEST_URI')));

        } elseif (strpos($extra, '/ru/ru') !== false) {
            $this->redirect($siteUrl . str_replace('/ru/ru', '/ru', $oInput->getServer('REQUEST_URI')));

        } elseif ($check && $check[strlen($check)-1] === '&') {
            $o = \System\Registry::connection()->createQuery("SELECT c
                FROM Data\Doctrine\Main\Content c
                WHERE c.pattern LIKE :pattern")
                ->setParameter('pattern', urldecode(substr($check, 0, -1)) . "%")
                ->setMaxResults(1)
                ->getOneOrNullResult();
            if ($o) {
                $this->redirect($template->getUrl($o->getPattern(), \Defines\Extension::HTML, $o->getLanguage()));
            }
        }
    }

    /**
     * Check deprecated `ua`
     */
    public function checkUkraine()
    {
        if (\System\Registry::translation()->getTargetLanguage() === \Defines\Language::UA) {
            $url = (new \Engine\Response\Template)->getUrl(
                (new \Engine\Request\Input)->getUrl(null), null, \Defines\Language::UK
            );
            $this->redirect($url);
        }
    }

    /**
     * Check previous adresses
     */
    public function checkBackward()
    {
        // Backward compatibility (ru.creativity.by -> creatibity.by/ru/index.html)
        $request = new \Engine\Request\Input();
        $lang = (new Backward)->getLanguage();
        if (!$lang) {
            $lang = \Defines\Language::BE;
        }

        // Get main domain
        $host = '';
        $hostAttr = explode('.', $request->getServer('HTTP_HOST'));
        $sz = sizeof($hostAttr) - 1;
        if ($sz >= 1) {
            $host = "{$hostAttr[$sz-1]}.{$hostAttr[$sz]}";
        }

        $urlList = \System\Registry::config()->getUrlList();
        $urlList[] = 'http://creativity.by';
        if (
                !\System\Registry::cron()
                && !in_array('http://' . $host, $urlList)
                && !in_array('https://' . $host, $urlList)
        ) {
            $url = \System\Registry::config()->getUrl($lang);
            $this->redirect("{$url}/index.html");
        }
    }
}
