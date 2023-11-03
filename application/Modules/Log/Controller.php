<?php namespace Modules\Log;

/**
 * General controller for index page
 * @see \Modules\AbstractController
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Log
 */
class Controller extends \Modules\AbstractController
{

    protected function initAllowed()
    {
        $oAccess = new \Access\Allowed();
        $oAccess->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindNullKey()
            ->addRequestMethod(\Defines\RequestMethod::GET)
                ->bindExtension(\Defines\Extension::HTML)
                    ->bindKey('/0', array('list' => \Defines\User\Account::getTextList(true)))
                ->copyToExtension(\Defines\Extension::JSON);

        if ($this->action === 'shareAction') {
            $oAccess->bindKey('/0', array('sanitize' => FILTER_SANITIZE_STRING))
                ->addRequestMethod(\Defines\RequestMethod::POST)
                ->bindExtension(\Defines\Extension::JSON)
                    ->bindKey('action', array('sanitize' => FILTER_SANITIZE_STRING))
                    ->bindKey(\Access\User::COOKIE_AUTH, array('type' => 'string'));
        }

        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    /**
     *
     * @param array $aParams
     * @return \Layouts\Helper\Redirect
     */
    public function indexAction(array $aParams)
    {
        $oHelper = new \Layouts\Helper\Redirect($this->request, $this->response);
        $oHelper->setUrl(
            (new \Engine\Response\Template)->getUrl('log/in/mail')
        );
        return $oHelper;
    }

    public function shareAction(array $aParams)
    {
        $base = $this->input->getUrlProtocol() . '://' . $this->input->getServer('HTTP_HOST');
        $url = $this->input->getServer('HTTP_ORIGIN', $base);
        if (!in_array($url, \System\Registry::config()->getUrlList(), true)) {
            $code = \Defines\Response\Code::E_FORBIDDEN;
            throw new \Error\Validation(\Defines\Response\Code::getHeader($code), $code);
        }

        $this->response->header('Access-Control-Allow-Origin', $url);
        $this->response->header('Access-Control-Allow-Credentials', 'true');
        $this->response->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS');
        $this->response->header('Access-Control-Max-Age', '10000');
        $this->response->header('Access-Control-Allow-Headers', 'Content-Type');
        $this->input->setCookie(\Access\User::COOKIE_AUTH, $this->input->getPost(\Access\User::COOKIE_AUTH));

        return new \Layouts\Helper\Initial($this->request, $this->response);
    }

    /**
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function inAction(array $aParams)
    {
        $sTargetAuth = (new \System\Aggregator)->getValue($aParams, 0, 'mail');

        switch ($sTargetAuth) {
            case 'mail':
                $this->response->meta(new \Engine\Response\Meta\Script('lib/aes'));
                break;

            case 'vk':
                $this->response->meta(new \Engine\Response\Meta\Script('//vk.com/js/api/openapi.js'));
                $this->response->meta(new \Engine\Response\Meta\Script('lib/widget/vk/auth'));
                break;

            case 'facebook':
                $lang = \System\Registry::translation()->getTargetLanguage();
                $locale = \Engine\Response\Meta\Ogp\Locales::getLocale($lang);
                $this->response->meta(new \Engine\Response\Meta\Script("lib/widget/facebook/{$locale}.js?_v=" . time()));
                $this->response->meta(new \Engine\Response\Meta\Script("lib/widget/facebook/auth"));
                break;
        }

        $oHelper = new \Layouts\Helper\Login($this->request, $this->response);
        $oHelper->add("login/$sTargetAuth", $aParams);
        return $oHelper;
    }

    /**
     * Logout action
     *
     * @param array $aParams
     * @return \Layouts\Helper\Basic
     */
    public function outAction(array $aParams)
    {
        \System\Registry::user()->out();

        $oHelper = new \Layouts\Helper\Redirect($this->request, $this->response);
        $oHelper->setUrl((new \Engine\Response\Template)->getUrl('index'));
        return $oHelper;
    }

}
