<?php namespace Engine\Book\Search;

use ApaiIO\Configuration\GenericConfiguration;
use ApaiIO\Operations\Search;
use ApaiIO\ApaiIO;
use ApaiIO\Operations\Lookup;

/**
 * Amazon API helper
 *
 * @since 2016-04-14
 * @author Viachaslau Lyskouski
 * @package Engine/Search
 */
class Amazon extends SearchAbstract
{

    /**
     * @var ApaiIO
     */
    protected $api;

    /**
     * Init Amazon API connection
     * @return ApaiIO
     */
    public function __construct()
    {
        $config = \System\Registry::config();
        $apiAmazon = $config->getSocialApi('amazon');

        $conf = new GenericConfiguration();
        $conf->setCountry('com')
            ->setAccessKey($apiAmazon['accessKey'])
            ->setSecretKey($apiAmazon['secretKey'])
            ->setAssociateTag($apiAmazon['tag']);

        $this->api = new ApaiIO($conf);
    }

    /**
     * Fill values by using Amazon API
     *
     * @param \SimpleXMLElement $result
     * @param \Engine\Book\Result\BookList $bookList
     * @return \Engine\Book\Result\BookList
     */
    public function prepare($result, \Engine\Book\Result\BookList $bookList)
    {
        foreach ((array) $result->Items->Item as $item) {
            if (!$this->isbn) {
                $this->isbn = (string) $item->ItemAttributes->ISBN;
            }
            if (!$this->isbn) {
                continue;
            }
            /* @var $book \Engine\Book\Result\Book */
            $book = $bookList[$this->isbn];
            $book->setIsbn($this->isbn)
                ->setTitle((string) $item->ItemAttributes->Title)
                ->setDate((string) $item->ItemAttributes->PublicationDate)
                ->setImage((string) $item->LargeImage->URL)
                ->setAuthor(implode(', ', (array) $item->ItemAttributes->Author));

            if ($item->EditorialReviews) {
                $txt = (string) $item->EditorialReviews->EditorialReview->Content;
                $book->setDescription($txt);
                $book->setText($txt);
            }
            $book->setCategory(implode(', ', array_reverse($this->category(
                $item->BrowseNodes->BrowseNode[0],
                [$item->BrowseNodes->BrowseNode[0]->Name]
            ))));
            // Convert transliterate to russian letters
            if ($item->ItemAttributes->Languages->Language[0]->Name === 'Russian') {
                $conv = new \System\Converter\StringUtf();
                $book->setTitle($conv->transliterate($book->getTitle(), false));
                $book->setAuthor($conv->transliterate($book->getAuthor(), false));
                $book->setLanguage(\Defines\Language::RU);
            }
            $bookList[$this->isbn] = $book;
            $this->isbn = '';
        }
        return $bookList;
    }

    /**
     * Exec Book Amazon API
     *
     * @param string $isbn
     * @return \SimpleXMLElement
     */
    public function isbn($isbn)
    {
        $search = new Lookup();
        $search->setResponseGroup(['Large']);
        $search->setIdType(Lookup::TYPE_ISBN);
        $search->setItemId($isbn);
        return simplexml_load_string($this->api->runOperation($search));
    }

    /**
     * Exec Book Amazon API
     *
     * @param string $author
     * @param string $title
     * @return \SimpleXMLElement
     */
    public function search($author, $title)
    {
        $search = new Search();
        $search->setResponseGroup(['Large']);
        $search->setCategory('Books');
        if ($author) {
            $search->setAuthor($author);
        }
        if ($title) {
            $search->setTitle($title);
        }
        return simplexml_load_string($this->api->runOperation($search));
    }

    /**
     * Build category list
     *
     * @param \SimpleXMLElement $object
     * @param array $aKey
     * @return array
     */
    public function category($object, $aKey)
    {
        if ($object->Ancestors) {
            $aKey[] = (string) $object->Ancestors->BrowseNode->Name;
            $aKey = $this->category($object->Ancestors->BrowseNode, $aKey);

        }
        if ($object->Children) {
            $aKey[] = (string) $object->Children->BrowseNode[0]->Name;
            $aKey = $this->category($object->Children->BrowseNode[0], $aKey);
        }
        return $aKey;
    }
}
