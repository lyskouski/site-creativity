<?php namespace Engine\Book;

use Engine\Request\Page\Basic;

/**
 * Description of Isbn
 *
 * @author s.lyskovski
 */
class Isbn
{

    /**
     * Init request
     * @param integer $isbn
     */
    public function __construct($isbn)
    {
        try {
            $url = "http://www.isbn.org/xmljson.php?request_code=isbn_convert&request_data={%22isbn%22:%22{$isbn}%22}";
            $this->result = json_decode((new Basic)->get($url), true);
        } catch (\Exception $e) {
            $this->result = array(
                'results' => array(
                    'isbn' => $isbn,
                    'converted_isbn' => $isbn
                ),
                'error' => $e->getMessage()
            );
        }
    }

    public function getIsbn()
    {
        return $this->result['results']['isbn'];
    }

    public function getConvertedIsbn()
    {
        return $this->result['results']['converted_isbn'];
    }
}
