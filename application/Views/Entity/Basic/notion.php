<?php  /* @var $this \Engine\Response\Template */

$aData = $this->get('list', array('og:title' => []));
/* @var $oContent \Data\Doctrine\Main\Content */
foreach ($aData['og:title'] as $sPattern => $oContent):
    $s = $oContent->getPattern();
    $author = $oContent->getAuthor();
    echo $this->partial('Basic/notion', array(
        'author' => $author ? $author->getUsername() : '',
        'title' => $oContent->getContent(),
        'href' => $oContent->getPattern(),
        'language' => $oContent->getLanguage(),
        'async' => false,
        'img_type' => $aData['image'][$s],
        'img' => $aData['og:image'][$s],
        'text' => $aData['description'][$s],
        'updated_at' => $oContent->getUpdatedAt()->format(\Defines\Database\Params::DATE_FORMAT)
    ));
endforeach;
