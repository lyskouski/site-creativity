<?php namespace System;

/**
 * Helper library for CryptoJS AES encryption/decryption
 * Allow you to use AES encryption on client side and server side vice versa
 *
 * @author BrainFooLong (bfldev.com)
 * @link https://github.com/brainfoolong/cryptojs-aes-php
 * @since 2015-10-05
 * @package System
 */
class CryptoJS
{

    const SALT = 's';
    const DATA = 'ct';
    const VECTOR = 'iv';

    /**
     * Decrypt data from a CryptoJS json encoding string
     *
     * @param mixed $passphrase
     * @param array $aData - json_decode({s: .., ct: .., iv: ...}, true)
     * @return mixed
     */
    public function decrypt($passphrase, $aData)
    {
        $salt = hex2bin($aData[self::SALT]);
        $ct = base64_decode($aData[self::DATA]);
        $iv = hex2bin($aData[self::VECTOR]);
        $concatedPassphrase = $passphrase . $salt;
        $md5 = array();
        $md5[0] = md5($concatedPassphrase, true);
        $result = $md5[0];
        for ($i = 1; $i < 3; $i++) {
            $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
            $result .= $md5[$i];
        }
        $key = substr($result, 0, 32);
        $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
        return json_decode($data, true);
    }

    /**
     * Encrypt value to a cryptojs compatiable json encoding string
     *
     * @param mixed $passphrase
     * @param mixed $value
     * @return array
     */
    public function encrypt($passphrase, $value)
    {
        $salt = openssl_random_pseudo_bytes(8);
        $salted = '';
        $dx = '';
        while (strlen($salted) < 48) {
            $dx = md5($dx . $passphrase . $salt, true);
            $salted .= $dx;
        }
        $key = substr($salted, 0, 32);
        $iv = substr($salted, 32, 16);
        $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
        return array(
            self::DATA => base64_encode($encrypted_data),
            self::VECTOR => bin2hex($iv),
            self::SALT => bin2hex($salt)
        );
    }

    public function getPassphrase($sInit = null)
    {
        if (is_null($sInit)) {
            $sInit = md5(date(\Defines\Database\Params::DATE_FORMAT) . __CLASS__);
        }
        return $sInit;
    }
}
