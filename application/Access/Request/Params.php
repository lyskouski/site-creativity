<?php namespace Access\Request;

/**
 * Description of Isbn
 *
 * @since 2016-05-16
 * @author Viachaslau Lyskouski
 */
class Params
{
    public function getIsbn($isbn = null, $triggerErr = true)
    {
        if (is_null($isbn)) {
            $isbn = (new \Engine\Request\Input)->getParam('isbn');
        }
        $value = preg_replace("/[^0-9]/","", (string) $isbn);

        // Zero behind numbers
        if ($value && strlen($value) < 10) {
            $value = str_repeat('0', 10 - strlen($value)) . $value;
        }

        if (!in_array(strlen($value), [0, 10, 13])) {
            $text = \System\Registry::translation()->sys('LB_ERROR_VALUE_PATTERN');
            if ($triggerErr) {
                throw new \Error\Validation($text . ' ISBN: [0-9]{10, 13}', \Defines\Response\Code::E_BAD_REQUEST);
            }
            $value = '';
        }
        return $value;
    }
}
