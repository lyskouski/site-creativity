<?php namespace Modules\Log\Auth\Whatsup;

use Engine\Response\Meta\Script;
use Layouts\Helper\Basic;

/**
 * General controller for email Authentication
 * @see \Modules\AbstractController
 *
 * Registry logic:
 * 1. Validate mail and password length
 * 1.1. AES crypt is used on front-end to send password
 * 2. Create a row in `user_account` (linked to a nullable-user[id=0])
 * 2.1. Pssw is used for AES crypt as a key
 * 3. Put mail into garbage collector - cron_task_mail
 * 3.1. Send mail by cron
 * 4. Forward to \Modules\Log\Auth\Controller to enter username and requested token
 *
 * Authentication logic:
 * 1. AES crypt password
 * 2. Descrypt password and use it for decryption of the database data
 * 3. Compare decrypted value with `updated_at`-field
 *
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
                ->bindNullKey();
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
        $oHelper = new Basic($this->request, $this->response);
        $oHelper->add('index');
        return $oHelper;
    }
}
