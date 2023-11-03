<?php namespace Modules\Log\Auth\Facebook;

use Engine\Response\Meta\Script;

/**
 * General controller for facebook Authentication
 * @see \Modules\AbstractController
 *
 * @note guzzlehttp/guzzle (Allows for implementation of the Guzzle HTTP client)
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
                    ->bindKey('userID', array('ctype' => 'integer'))
                    ->bindKey('signed_request')
                    ->bindKey('accessToken')
                    ->bindKey('name');
        return $oAccess->isReach($this->request->getRequestMethod(), $this->request->getResponseType());
    }

    /**
     * User Authentication
     *
     * @param array $aParams
     * @return \Layouts\Helper\Redirect
     */
    public function indexAction(array $aParams)
    {
        $oModel = new Model();
        $bResult = $oModel->login($this->input->getPost());
        // if registration was not finished -> show registry page
        if (is_null($bResult)) {
            $aData = array(
                'account' => $oModel->getMessage(),
                'type' => \Defines\User\Account::FACEBOOK
            );
            return $this->forward('Log\Auth', 'index', $aData);

        } elseif ($bResult) {
            $this->response->meta(new Script('', "window.location = jQuery('.el_top_button')[0].href;"));

        } else {
            throw new \Error\Validation('Incorrect Authentication params');
        }

        $oResponser = new \Layouts\Helper\Login($this->request, $this->response);
        $oResponser->add('ok');
        return $oResponser;
    }

}
