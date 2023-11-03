<?php namespace Modules\Book\Recite\Import;

use Defines\Response\Code;
use Defines\Database\CrMain;

/**
 * Recite Model
 *
 * @since 2016-12-19
 * @author Viachaslau Lyskouski
 */
class Model
{
    // PocketBook definitions
    const ID_CITE = 32;
    const ID_COMMENT = 64;
    const ID_CONTENT = 1;

    protected $bookList;

    /**
     * @param string $isbn
     * @return \Data\Doctrine\Main\Book
     * @throws \Error\Validation
     */
    public function getBook($isbn)
    {
        if (!isset($this->bookList[$isbn])) {
            $em = \System\Registry::connection();
            $this->bookList[$isbn] = $em->find(CrMain::BOOK, ltrim($isbn, '0'));
            if (!$this->bookList[$isbn]) {
                $code = Code::E_NOT_FOUND;
                throw new \Error\Validation(Code::getHeader($code), $code);
            }
        }
        return $this->bookList[$isbn];
    }

    public function parseCite($content, $idType = self::ID_CITE, $result = array())
    {
        $page = array();
        $tmp = explode('<!-- type="' . $idType . '"', $content);
        unset($tmp[0]);
        foreach ($tmp as $txt) {
            preg_match('/position="#(\S+)\([a-zA-Z0-9]+,(\d+)/', $txt, $page);
            if (strpos($txt, '<!--') !== false) {
                $txt = substr($txt, 0, strpos($txt, '<!--'));
            }
            $txt = substr($txt, strpos($txt, '--!>') + 4);
            if (substr_count($txt, '<font') > 1) {
                $txt = substr($txt, strpos($txt, '</font>'));
            }
            $doc = new \System\Converter\Content($txt);
            if (!isset($page[2]) || !trim($doc->getText())) {
                continue;
            }
            $txt = $doc->getText();
            if (!isset($result[$page[2]])) {
                $result[$page[2]] = $txt;
            } else {
                $result[$page[2]] .= '...<br />' . $txt;
            }
        }
        return $result;
    }

    public function addCite($isbn, $cite, $page)
    {
        $em = \System\Registry::connection();
        $book = $this->getBook($isbn);
        if ($page > $book->getPages()) {
            $code = Code::E_CONFLICT;
            throw new \Error\Validation(
                Code::getHeader($code) . ": $page > {$book->getPages()}",
                $code
            );
        }

        $citeObj = $em->getRepository(\Defines\Database\CrMain::CONTENT)->findOneBy(array(
            'language' => $book->getContent()->getLanguage(),
            'author' => \System\Registry::user()->getEntity(),
            'pattern' => $book->getContent()->getPattern() . '/' . $page
        ));
        if ($citeObj) {
            $content = new \System\Converter\Content($citeObj->getContent() . '...<br />' . $cite);

            $citeObj->setContent($content->getHtml(true))
                ->setSearch($content->getText())
                ->setAuditor(null);
        } else {
            $content = new \System\Converter\Content($cite);

            $citeObj = new \Data\Doctrine\Main\Content();
            $citeObj->setAuthor(\System\Registry::user()->getEntity())
                ->setType('quote')
                ->setContent($content->getHtml(true))
                ->setSearch($content->getText())
                ->setContent2($book->getContent())
                ->setLanguage($book->getContent()->getLanguage())
                ->setPattern($book->getContent()->getPattern() . '/' . $page);
        }

        $citeObj->setAccess(\Defines\User\Access::getAudit())
            ->setUpdatedAt(new \DateTime);

        $em->persist($citeObj);
        $em->flush($citeObj);
        return $citeObj->getId();
    }

    public function parseNav($content)
    {
        $result = array();
        $page = array();
        $lvl = array();
        // type="1" level="{level}" position="#pdfloc(41de,{page})" --!>{txt}<!--
        $tmp = explode('<!-- type="1"', $content);
        unset($tmp[0]);
        foreach ($tmp as $txt) {
            preg_match('/level="(\d+)"/', $txt, $lvl);
            preg_match('/position="#(\S+)\((\S+),(\d+)/', $txt, $page);
            if (strpos($txt, '<!--') !== false) {
                $txt = substr($txt, 0, strpos($txt, '<!--'));
            }
            $doc = new \System\Converter\Content(substr($txt, strpos($txt, '--!>') + 4));
            if (isset($lvl[1]) && isset($page[3]) && $doc->getText()) {
                $result[] = "\content{{$lvl[1]},{$page[3]},{$doc->getText()}}";
            }
        }
        return implode('', $result);
    }

    public function addNav($isbn, $nav)
    {
        $em = \System\Registry::connection();
        $page = $this->getBook($isbn)->getContent();

        $citeObj = new \Data\Doctrine\Main\Content();
        $citeObj->setType('nav')
            ->setContent($nav)
            ->setUpdatedAt(new \DateTime)
            ->setSearch('')
            ->setLanguage($page->getLanguage())
            ->setPattern($page->getPattern());

        $em->persist($citeObj);
        $em->flush($citeObj);
        return $citeObj->getId();
    }
}
