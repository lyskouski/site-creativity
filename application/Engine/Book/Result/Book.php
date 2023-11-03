<?php namespace Engine\Book\Result;

/**
 * Viewer functionality
 *
 * @since 2016-04-14
 * @author Viachaslau Lyskouski
 * @package Engine/Search/Result
 */
class Book
{

    protected $isbn;
    protected $udc = '';
    protected $title;
    protected $author;
    protected $image = '/img/css/el_notion/work/book.svg';
    protected $imageType = '/img/css/el_notion/work/book_type.svg';
    protected $date;
    protected $description;
    protected $text;
    protected $category;
    protected $pageCount;
    protected $language;

    function getIsbn()
    {
        //if ($this->isbn && strlen($this->isbn) < 13) {
        //    $this->isbn = (new \Access\Request\Params)->getIsbn(
        //        (new \Engine\Book\Isbn($this->isbn))->getIsbn()
        //    );
        //}
        return $this->isbn;
    }

    /**
     * Set ISBN
     *
     * @param string $isbn
     * @return \Engine\Book\Result\Book
     */
    function setIsbn($isbn)
    {
        $this->isbn = (string) $isbn;
        return $this;
    }

    function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return \Engine\Book\Result\Book
     */
    function setTitle($title)
    {
        if ($title) {
            $this->title = $title;
        }
        return $this;
    }

    function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set title
     *
     * @param string $author
     * @return \Engine\Book\Result\Book
     */
    function setAuthor($author, $clear = true)
    {
        if ($author && !$clear) {
            $tmp = explode(', ', $this->author);
            $tmp[] = $author;
            $this->author = implode(', ', $tmp);
        } elseif ($author) {
            $this->author = $author;
        }
        return $this;
    }

    function getImage()
    {
        return $this->image;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return \Engine\Book\Result\Book
     */
    function setImage($image)
    {
        if ($image) {
            $this->image = $image;
        }
        return $this;
    }

    function getImageType()
    {
        return $this->imageType;
    }

    /**
     * Set image type
     *
     * @param string $imageType
     * @return \Engine\Book\Result\Book
     */
    function setImageType($imageType)
    {
        $this->imageType = $imageType;
        return $this;
    }

    function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     *
     * @param string $date
     * @return \Engine\Book\Result\Book
     */
    function setDate($date)
    {
        if ($date) {
            $this->date = $date;
        }
        return $this;
    }

    function getUdc()
    {
        return $this->udc;
    }

    /**
     * Set udc
     *
     * @param string $udc
     * @return \Engine\Book\Result\Book
     */
    function setUdc($udc)
    {
        $this->udc = $udc;
        return $this;
    }

    function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return \Engine\Book\Result\Book
     */
    function setDescription($description)
    {
        if ($description) {
            $this->description = (new \System\Converter\StringUtf)->substr(strip_tags($description), 0, 120);
        }
        return $this;
    }

    function getText()
    {
        return $this->text;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return \Engine\Book\Result\Book
     */
    function setText($text)
    {
        if ($text) {
            $this->text = $text;
        }
        return $this;
    }

    function getCategory()
    {
        return $this->category;
    }

    /**
     * Set category
     *
     * @param string $category
     * @return \Engine\Book\Result\Book
     */
    function setCategory($category)
    {
        if ($category) {
            $this->category = $category;
        }
        return $this;
    }

    function getPageCount()
    {
        return $this->pageCount;
    }

    /**
     * Set page count
     *
     * @param integer $pageCount
     * @return \Engine\Book\Result\Book
     */
    function setPageCount($pageCount)
    {
        if ($pageCount) {
            $this->pageCount = $pageCount;
        }
        return $this;
    }

    function getLanguage()
    {
        if (!$this->language) {
            $this->language = \System\Registry::translation()->getTargetLanguage();
        }
        return $this->language;
    }

    /**
     * Set language
     *
     * @param integer $language
     * @return \Engine\Book\Result\Book
     */
    function setLanguage($language)
    {
        $this->language = $language;
        return $this;
    }

}
