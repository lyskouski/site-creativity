<?php namespace Error;

use Engine\Request\Params;

/**
 * Abstract class for exceptions
 * @see TextInterface
 *
 * @author Viachaslau Lyskouski
 * @since 2015-06-18
 * @package System
 */
abstract class TextAbstract extends \Exception implements TextInterface
{

    const E_MESSAGE = 'message';
    const E_CODE = 'code';
    const E_FILE = 'file';
    const E_LINE = 'line';
    const E_TRACE = 'trace';

    public function plotErrorPage()
    {
        $sPath = $this->getTemplateName();
        if (!$sPath) {
            return;
        }

        $sErrMessage = $this->getMessage();

        $oParams = new Params(false);

        try {
            \System\Registry::user();
        } catch (\Exception $ex) {
            \System\Registry::setUser(new \Access\User(null));
            $sErrMessage .= ' [Missing authorisation]';
        }

        $aParams = array(
            self::E_MESSAGE => $sErrMessage,
            self::E_CODE => (int) $this->getCode(),
            self::E_FILE => $this->getFile(),
            self::E_LINE => $this->getLine(),
            self::E_TRACE => $this->getTrace()
        );

        if (!$aParams[self::E_CODE]) {
            $aParams[self::E_CODE] = \Defines\Response\Code::E_BAD_REQUEST;
        }

        $oResponse = new \Engine\Response();
        $oResponse->setLayoutType($oParams->getResponseType());

        $sSiteName = \System\Registry::translation()->sys('LB_SITE_TITLE');
        $oResponse->title($sSiteName);
        $oResponse->title($sErrMessage);
        $oResponse->meta(new \Engine\Response\Meta\Meta('viewport', 'width=device-width,minimum-scale=1,initial-scale=1'), true);

        $oHelper = new \Layouts\Helper\Initial($oParams, $oResponse);
        if ($oParams->getResponseType() === \Defines\Extension::JSON) {
            $oHelper->add('Basic/null', $aParams);
        } else {
            $oParams->setResponseType(\Defines\Extension::HTML);
            $oResponse->setLayoutType($oParams->getResponseType());
            $oHelper->add($sPath, $aParams);
        }

        if ($oParams->getResponseType() === \Defines\Extension::HTML) {
            $aParams['language'] = \System\Registry::translation()->getTargetLanguage();
            $aParams['aside'] = true;
            $oHelper->add(
                'Basic/footer',
                $aParams,
                \Engine\Response\Template::getDefaultPath()
            );
        }

        $oHelper->sendResponse();
    }
}
