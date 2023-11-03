<?php /* @var $this \Engine\Response\Template */

$translate = \System\Registry::translation();
$imgPath = new \System\Minify\Images();

/* @var $o \Data\Doctrine\Main\Content */
foreach ($this->get('list') as $o):
    echo $this->partial('Basic/notion', array(
        'author' => \Data\UserHelper::getUsername($o->getAuthor()),
        'title' => $translate->get(['og:title', $o->getPattern()]),
        'href' => $o->getPattern(),
        'async' => false,
        'img_type' => $translate->get(['image', $o->getPattern()], null, $imgPath->adaptWork($o->getPattern(), '_type')),
        'img' => $translate->get(['og:image', $o->getPattern()], null, $imgPath->adaptWork($o->getPattern())),
        'text' => $translate->get(['description', $o->getPattern()]),
        'updated_at' => $o->getUpdatedAt()->format(\Defines\Database\Params::DATE_FORMAT)
    ));
endforeach;
