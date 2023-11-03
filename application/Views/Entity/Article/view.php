<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content">
    <p>&nbsp;</p>
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $this->get('og:title'),
        'title_href' => $this->getUrl($this->get('pattern')),
        'subtitle' => $translate->sys('LB_PERSON_DRAFT'),
        'subtitle_href' => $this->getUrl('/person/work')
    ));
    ?>

    <section class="el_panel bs_recess bg_mask">
        <header class="bg_attention fs_small"><?php echo $translate->sys('LB_PERSON_SUBMIT_PUBLIC') ?></header>
        <p><?php echo $translate->sys('LB_PUBLICATION_WAIT_AUDIT') ?></p>
    </section>

    <section class="indent">
            <?php
            /* @var $o \Data\Doctrine\Main\ContentNew */
            foreach ($this->get('list') as $o):
                if (strpos($o->getType(),'content#') === 0):
                    ?><section class="indent el_border bg_highlight el_A4"><?php echo $o->getContent() ?></section><?php
                endif;
            endforeach;
            ?>
    </section>
</article>