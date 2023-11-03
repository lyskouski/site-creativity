<?php namespace Modules\Dev\History;

/**
 * History of changes controller for index page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Controller extends \Modules\AbstractController
{

    protected function initAllowed()
    {
        $oAccess = new \Access\Allowed();
        $oAccess->addRequestMethod(\Defines\RequestMethod::GET)
            ->bindExtension(\Defines\Extension::HTML)
            ->bindKey('/0', array('ctype' => 'integer'));

        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    public function indexAction(array $aParams)
    {
        throw new \Error\Validation(
            \System\Registry::translation()->sys('LB_HEADER_400'),
            \Defines\Response\Code::E_BAD_REQUEST
        );
    }

    public function indexNumAction(array $aParams)
    {
        $oResult = new \Layouts\Helper\Dev($this->request, $this->response);
        $history = (new Model)->getHistory($aParams[0]);

        $links = array();
        $tmp = explode('/', $history['current']->getPattern());
        for ($i = 1; $i <= sizeof($tmp); $i++) {
            $links[] = implode('/', array_slice($tmp, 0, $i));
        }
        $list = \System\Registry::connection()->getRepository(\Defines\Database\CrMain::CONTENT)->findBy(array(
            'pattern' => $links,
            'type' => \Defines\Content\Attribute::TYPE_TITLE,
            'language' => \System\Registry::translation()->getTargetLanguage()
        ));

        $validate = new \Access\Validate\Check();
        $validate->setType(\Defines\User\Access::READ);
        foreach ($list as $o) {
            if (!$validate->isAccepted($o)) {
                $oTranslate = \System\Registry::translation();
                throw new \Error\Validation($oTranslate->sys('LB_HEADER_423'), \Defines\Response\Code::E_LOCKED);
            }
        }

        $oResult->add('index', $history);
        return $oResult;
    }
}
