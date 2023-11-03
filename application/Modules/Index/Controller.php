<?php namespace Modules\Index;

use Engine\Response\Meta\Meta;

/**
 * General controller for index page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Controller extends \Modules\AbstractController
{

    protected function initAllowed()
    {
        // Check old links (backward compatibility)
        if ($this->action !== 'robotsAction' && (new \Deprecated\Backward)->checkRedirect()) {
            exit;
        }

        $oAccess = new \Access\Allowed();
        $oAccess->addRequestMethod(\Defines\RequestMethod::GET)
            ->bindExtension(\Defines\Extension::HTML)
                ->bindNullKey()
            ->bindExtension(\Defines\Extension::TXT)
                ->bindNullKey()
            ->bindExtension(\Defines\Extension::XML)
                ->bindKey('/0', array('pattern' => '/sitemap(\d){1,}/'));

        if ($this->params) {
            $this->action = 'sitemapAction';
        }

        $url = $this->input->getServer('REQUEST_URI');
        if ($url === '/' || $url === '') {
            $this->action = 'mainAction';
        }

        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    public function mainAction(array $aParams)
    {
        // In creativity.by the summary page only
        if ($this->input->getServer('HTTP_HOST') !== 'creativity.by') {
            (new \Deprecated\Migration)->redirect(
                (new \Engine\Response\Template)->getUrl('index')
            );
        }

        $oTranslate = \System\Registry::translation();
        $oTranslate->setTargetLanguage(\Defines\Language::EN);

        $oHelper = new \Layouts\Helper\Initial($this->request, $this->response);

        $title = $oTranslate->sys('LB_SITE_TITLE');
        $this->response->titleOverride(trim(substr($title, strpos($title, '«') + 2), '»') . ' - creativity.by', false);
        // Yandex verification
        $this->response->meta(new Meta('yandex-verification', '7e9f047eeafc7be9'));

        $menu = array();
        foreach (\Defines\Language::getList() as $lang) {
            $menu[$lang] = array(
                'title' => $oTranslate->sys('LB_LANG_' . strtoupper($lang), $lang),
                'href' => (new \Engine\Response\Template)->getUrl('index', null, $lang)
            );
        }

        $params = array(
            'menu' => $menu,
            'ext' => $this->request->getResponseType(),
            'list' => (new Model)->getLastUpdate()
        );
        $oHelper->add('Basic/header', $params);
        $oHelper->add('main', $params);
        $oHelper->add('Basic/footer');
        return $oHelper;
    }

    public function indexAction(array $aParams)
    {
        $oRepository = (new \Data\ContentHelper)->getRepository();
        $aParams['oeuvre_list'] = $oRepository->findLastTopics('oeuvre/%', 0, 6);
        $aParams['mind_list'] = $oRepository->findLastTopics('mind/%', 0, 4);
        $aParams['news_list'] = (new \Modules\Dev\Model)->getList('dev/news', 0, 4);

        $tr = \System\Registry::translation();

        $ya = new \System\ArrayUndef(\System\Registry::config()->getSocialApi('yandex-verification'));
        $ya->setUndefined($ya['default']);
        $this->response->meta(new Meta('yandex-verification', $ya[$tr->getTargetLanguage()]), true);

        // Title override
        $this->response->titleOverride($tr->sys('LB_SITE_TITLE'), false);

        return $this->getSection($aParams, 'index', 0);
    }

    public function robotsAction(array $aParams)
    {
        // Only txt format
        if ($this->request->getResponseType() !== \Defines\Extension::TXT) {
            throw new \Error\Validation(
                \System\Registry::translation()->sys('LB_HEADER_405'),
                \Defines\Response\Code::E_NOT_ALLOWED
            );
        }

        $result = new \Layouts\Helper\Initial($this->request, $this->response);
        $result->add('robots/agent');
        if (\System\Registry::config()->getIndexing()) {
            $result->add('robots/enable');
            $result->add('robots/sitemap');
        } else {
            $result->add('robots/disable');
        }
        return $result;
    }

    public function sitemapAction(array $aParams)
    {
        $result = new \Layouts\Helper\Initial($this->request, $this->response);
        $oTranslate = \System\Registry::translation();
        $dir = realpath(\System\Registry::config()->getPublicPath() . '/data/sitemap/' . $oTranslate->getTargetLanguage());

        if (!$dir || $this->request->getResponseType() !== \Defines\Extension::XML) {
            throw new \Error\Validation(
                $oTranslate->sys('LB_HEADER_406'),
                \Defines\Response\Code::E_NOT_ACCEPTABLE
            );
        }
        if ($aParams) {
            $filename = "$dir/{$aParams[0]}.xml";
            if (!file_exists($filename)) {
                throw new \Error\Validation(
                    $oTranslate->sys('LB_HEADER_410'),
                    \Defines\Response\Code::E_DELETED
                );
            }
            $result->add('sitemap/url', array('path' => $filename));
        } else {
            $o = new \FilesystemIterator($dir, \FilesystemIterator::SKIP_DOTS);
            $result->add('sitemap/list', array('count' => $o));
        }
        return $result;
    }

    public function licenseAction(array $aParams)
    {
        return $this->getSection($aParams, 'license', 0);
    }
}
