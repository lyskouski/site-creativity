<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
?>
<article class="el_content">
    <?php
    echo $this->partial(
        'Basic/title',
        array(
            'title' => $this->get('username'),
            'title_href' => $this->getUrl('person/' . $this->get('username')),
            'subtitle' => $translate->sys("{$this->get('subtitle')}"),
            'languages' => \Defines\Language::getList()
        )
    );

    if ($this->get('list')):
        echo $this->partial('Entity/Basic/list');
    else:
        ?><section class="indent"><?php echo $translate->sys('LB_HEADER_204') ?><section><?php
    endif;

    echo $this->partial('Basic/Nav/pages', array(
        'curr' => $this->get('curr'),
        'num' => \Defines\Database\Params::getPageCount($this->get('num'), $this->get('count_page')),
        'url' => $this->get('url')
    ));
    ?>
</article>