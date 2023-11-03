<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
?>
<article class="el_content">
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_SITE_UPDATES'),
        'subtitle' => $translate->sys('LB_SITE_UPDATES', \Defines\Language::RU)
    ));
    echo $this->partial('Entity/Basic/notion', array(
        'list' => $this->get('list')
    ));

    echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_LANGUAGE_SELECTION'),
        'subtitle' => $translate->sys('LB_LANGUAGE_SELECTION', \Defines\Language::RU),
        'num' => 2
    ));
    ?>
    <section class="el_table_pair">
        <?php foreach ($this->get('menu') as $lang => $menu): ?>
        <div class="indent el_table ui cr_pointer" data-class="View/Href" data-actions="target" data-target=".ui-target-<?php echo $lang ?>">
            <strong class="indent im_center"><span class="im_lang im_lang_<?php echo $lang ?>">&nbsp;</span></strong>
            <strong><?php echo $menu['title'] ?>:</strong>
            <a hreflang="<?php echo $lang ?>" class="co_attention ui-target-<?php echo $lang ?>" href="<?php echo $menu['href'] ?>"><?php echo $translate->sys('LB_SITE_TITLE', $lang) ?></a>

            <blockquote class="indent_neg_inline"><?php echo $translate->get(['description', 'index'], $lang) ?></blockquote>
        </div>
        <?php endforeach ?>
    </section>

    <p class="clear">&nbsp;</p>
</article>