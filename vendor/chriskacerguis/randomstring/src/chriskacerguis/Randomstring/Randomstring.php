<?php
/**
 * Simple easy to use Randomstring class
 *
 * @author  Chris Kacerguis <public@cold.io>
 * @link    https://github.com/chriskacerguis/randomstring
 * @license MIT
 */

namespace chriskacerguis\Randomstring;

class Randomstring
{

    /**
     * the host of the server you are connecing to
     * @var string
     */
    protected $alphanum = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    /**
     * SSH username that you are connecting as
     * @var string
     */
    protected $chars    = '`-=~!@#$%^&*()_+,./<>?;:[]{}\|';

    /**
     * the constructor
     * @author Chris Kacerguis <public@cold.io>
     */
    public function __construct()
    {

    }

    /**
     * generates a random password based on the string above.  It is built to 
     * be "enough" and fast for most applications, however for cryptographically 
     * secure applications, you should use openssl_random_pseudo_bytes()
     * 
     * @author Chris Kacerguis <public@cold.io>
     * @param  int  $length         the number of charecters of the string you want
     * @param  boolean $useChars    set to true if you want to use specical chars
     * @param  boolean $function    function wrapper to call on string before return (e.g. strtoupper)
     * @return string               the random string
     */
    public function generate($length, $useChars = false, $function = false)
    {
        $random = $this->alphanum;

        if ($useChars !== false) {
            $random = $this->alphanum.$this->chars;
        }

        $str = '';
        $max = strlen($random) - 1;

        for ($i = 0; $i < $length; $i++) {
            $str .= $random[mt_rand(0, $max)];
        }

        if ($function !== false && function_exists($function)) {
            $str = call_user_func($function, $str);
        }

        return $str;
    }



}
