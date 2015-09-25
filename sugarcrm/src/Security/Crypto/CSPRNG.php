<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

namespace Sugarcrm\Sugarcrm\Security\Crypto;

/**
 *
 * Cryptographic Secure Psuedo Random Number Generator
 *
 * This class should be used when in need of secure random numbers. It wraps
 * around different available secure random number generators. The random
 * numbers generated by this class are suitable for tokens, sessions, salt,
 * IV's, ...
 *
 * Note that this class should *NOT* be used to generate a huge amount of
 * random numbers as in certain cases this may degrade the quality.
 *
 */
class CSPRNG
{
    /**
     * @var CSPRNG
     */
    protected static $instance;

    /**
     * Available generators
     * @var array
     */
    protected $generators = array();

    /**
     * Ctor
     */
    public function __construct()
    {
        $this->generators = $this->getAvailableGens();
    }

    /**
     * Get instance
     * @return CSPRNG
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Generate secure random value.
     *
     * Note that when the encoded flag is set, the returned random string
     * uses only allowed base64 characters as per RFC-4648. It doesn't
     * mean that the binary random value is base64 encoded.
     *
     * @param integer $size Byte size
     * @param boolean $encode
     * @return string
     * @throws \RuntimeException
     */
    public function generate($size, $encode = false)
    {
        foreach ($this->generators as $name => $method) {

            $random = call_user_func(array($this, $method), $size);

            // try next one on failure
            if ($random === false || $this->binaryStrLen($random) !== $size) {
                continue;
            }

            return $encode ? $this->binaryEncode($random, $size) : $random;
        }

        throw new \RuntimeException(sprintf(
            'CSPRNG unable to generate random %s bytes (last used generator %s)',
            $size,
            $name
        ));
    }

    /**
     * Get available generators. Note that the orders of the returned
     * list matters where the most preferrable method should be listed
     * on the top.
     *
     * @return array
     */
    protected function getAvailableGens()
    {
        $generators = array();

        /*
         * PHP7 CSPRNG
         */
        if (function_exists('random_bytes')) {
            $generators['csprng'] = 'genCsprng';
        }

        /*
         * Use /dev/urandom on *nix and Microsoft's Crypto API
         */
        if (function_exists('mcrypt_create_iv')) {
            $generators['mcrypt'] = 'genMcrypt';
        }

        /*
         * For better or for worse, available for platforms on PHP >= 5.3
         */
        if (function_exists('openssl_random_pseudo_bytes')) {
            $generators['openssl'] = 'genOpenssl';
        }

        return $generators;
    }

    /**
     * Generator using PHP7 builtin CSPRNG
     * @param integer $size Byte size
     * @return string|false
     */
    protected function genCsprng($size)
    {
        try {
            $random = random_bytes($size);
        } catch (\Error $e) {
            $random = false;
        }
        return $random;
    }

    /**
     * Generator using mcrypt
     * @param integer $size Byte size
     * @return string|false
     */
    protected function genMcrypt($size)
    {
        return mcrypt_create_iv($size, MCRYPT_DEV_URANDOM);
    }

    /**
     * Generator using openssl
     * @param integer $size Byte size
     * @return string|false
     */
    protected function genOpenssl($size)
    {
        $random = openssl_random_pseudo_bytes($size, $isStrong);
        return $isStrong ? $random : false;
    }

    /**
     * Binary strlen wrapper avoiding mbstring.func_overload issues
     *
     * @param string $string Binary string
     * @return integer
     */
    protected function binaryStrLen($string)
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($string, '8bit');
        }
        return strlen($string);
    }

    /**
     * Binary substr wrapper avoiding mbstring.func_overload issues
     *
     * @param string $string Binary string
     * @param integer $start
     * @param integer $length
     * @return string
     */
    protected function binarySubStr($string, $start, $length)
    {
        if (function_exists('mb_substr')) {
            return mb_substr($string, $start, $length, '8bit');
        }
        return substr($string, $start, $length);
    }

    /**
     * Encode binary string using base64 allowed characters
     * as per RFC-4648 being [A-Z][a-z][0-9]+/
     *
     * @param string $string Binary string
     * @param integer $size
     * @return string
     */
    protected function binaryEncode($string, $size)
    {
        $string = rtrim(base64_encode($string), '=');
        return $this->binarySubStr($string, 0, $size);
    }
}
