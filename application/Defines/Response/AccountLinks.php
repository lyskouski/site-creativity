<?php namespace Defines\Response;

use Defines\User\Account;
/**
 * Compile response links
 *
 * @author Viachaslau Lyskouski
 * @since 2015-07-20
 * @package Defines/Response
 */
class AccountLinks
{

    public static function get($iType, $sAccount)
    {
        $sResult = '';
        switch ($iType) {
            case Account::MAIL:
                if (\System\Registry::user()->isAdmin()) {
                    $sResult = "mailto:$sAccount";
                }
                break;
        }
        return $sResult;
    }
}
