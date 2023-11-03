<?php

namespace Modules\Person\Accounts;

use Defines\User\Account;

/**
 * Model object for person page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\AbstractModel
{

    /**
     * Get all possible account for an Authentication
     * @return array
     */
    public function getAccounts()
    {

        $aResult = array();
        $oTranslate = \System\Registry::translation();
        $imgPath = new \System\Minify\Images();

        foreach (Account::getTextList() as $key => $sName) {
            $aColor = Account::getColor($key);
            $aResult[$key] = array(
                'title' => $oTranslate->sys("LB_AUTH_{$sName}"),
                'text' => $oTranslate->sys( "LB_AUTH_{$sName}_DESCRIPTION" ),
                'obj_img' => true,
                'img' => "{$imgPath->getAccount()}auth.svg#a={$aColor[0]},b={$aColor[1]}",
                'img_type' => $imgPath->adaptAccount($key, '_type'),
                'href' => '/log/in#!/' . strtolower($sName),
                'class' => '',
                'updated_at' => ''
            );
        }

        /* @var $oUserAccount \Data\Doctrine\Main\UserAccount */
        foreach (\System\Registry::user()->getAccounts() as $oUserAccount) {
            $aResult[$oUserAccount->getType()] = array_merge(
                    $aResult[$oUserAccount->getType()],
                    array(
                    //    'class' => 'el_new',
                        'class_title' => ' bg_accepted',
                        'updated_at' => $oUserAccount->getUpdatedAt()->format(\Defines\Database\Params::DATE_FORMAT),
                        'href' => '',
                        'text' => $oTranslate->sys( 'LB_AUTH_EXIST' ),
                    )
            );
            // @todo: timeout for vk, facebook ...
        }

        // @fixme: remove this section [clear not active accounts]
        foreach ($aResult as $key => $a) {
            if (!in_array($key, [Account::VK, Account::FACEBOOK, Account::MAIL])) {
                unset($aResult[$key]);
            }
        }

        return $aResult;
    }

}
