<?php /* @var $this \Engine\Response\Template */

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content">
    <p>&nbsp;</p>
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $this->get('title'),
        'title_href' => $this->getUrl($this->get('title_href')),
        'languages' => $this->get('languages', \Defines\Language::getList()),
        'subtitle' => $this->get('subtitle'),
        'subtitle_href' => $this->getUrl($this->get('subtitle_href'))
    ));
