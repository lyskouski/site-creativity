<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

$list = $this->get('list');
$bookList = array();
$tmp = \System\Registry::connection()->getRepository(\Defines\Database\CrMain::BOOK)->findById($list);
/* @var $o \Data\Doctrine\Main\Book */
foreach ($tmp as $o):
    $tmp["{$o->getId()}"] = $o;
endforeach;

foreach ($list as $id):
    if (!isset($tmp[$id])):
        continue;
    endif;
    $bookList[] = $tmp[$id];
endforeach;

$i = 0;
foreach ($bookList as $o):
    $img = $translate->get(['og:image', $o->getContent()->getPattern()], $o->getContent()->getLanguage());
    $i++;
    echo $this->partial('Basic/notion_plain', array(
        'author_txt' => $o->getAuthor(),
        'title' => $o->getTitle(),
        'href' => $this->getUrl($o->getContent()->getPattern(), null, $o->getContent()->getLanguage()) . '" target="_blank',
        'pageCount' => $o->getPages(),
        'book_style' => true,
        'async' => false,
        'img' => $img,
        'img_type' => $img,
        'entity' => $o->getContent(),
        'origin' => true,
        'draggable' => $this->get('draggable', true),
        'callback' =>  $this->get('callback', '') . '" data-isbn="' . $o->getId() . '" data-pos="' . $i,
        'text' => '',
        'book_aside' => $this->get('buttons', []),
        'updated_at' => $o->getYear()
    ));
endforeach;
