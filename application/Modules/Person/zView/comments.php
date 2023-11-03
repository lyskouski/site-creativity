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

    $aComents = $this->get('list', array());

    if (!$aComents):
        ?><section class="indent"><?php echo $translate->sys('LB_HEADER_204') ?><section><?php
    endif;

    $pattern = '';
    /* @var $oComment \Data\Doctrine\Main\Content */
    foreach ($aComents as $i => $oComment):
        if ($pattern !== $oComment->getPattern()):
        ?><section class="bg_form clear">
            <a class="indent" target="_blank" href="<?php echo $this->getUrl($oComment->getPattern()) ?>"><?php echo $translate->get(['og:title', $oComment->getPattern()]) ?></a>
        </section><?php
        endif;
        echo $this->partial('Entity/Basic/comment', array(
            'comment' => $oComment
        ));
    endforeach;

    echo $this->partial('Basic/Nav/pages', array(
        'curr' => $this->get('curr'),
        'num' => \Defines\Database\Params::getPageCount($this->get('num'), $this->get('count_page')),
        'url' => $this->get('url')
    ));
    ?>
</article>