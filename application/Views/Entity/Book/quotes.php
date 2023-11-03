<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$lang = $this->get('entity')->getLanguage();

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content" itemprop="mainEntity">
    <?php echo $this->partial('Entity/Book/nav_list', array(
       'url' => $this->get('url'),
       'url_active' => $this->get('url') . '/quote'
    )) ?>

    <header class="el_content_header">
        <h1 class="title"><a class="nowrap" href="<?php echo $this->getUrl($this->get('url')) ?>"><?php echo $translate->sys('LB_QUOTE') ?></a></h1>
    </header>

    <section class="indent clear">
        <?php echo $this->partial('Basic/notion_plain', array(
            'author_txt' => $translate->get(['author', $this->get('url')], $lang),
            'title' => $translate->get(['og:title', $this->get('url')], $lang),
            'href' => $this->get('url'),
            'pageCount' => $translate->get(['pageCount', $this->get('url')], $lang),
            'book_style' => true,
            'async' => false,
            'img' => $translate->get(['og:image', $this->get('url')], $lang),
            'img_type' => $translate->get(['og:image', $this->get('url')], $lang),
            'entity' => $this->get('entity'),
            'origin' => true,
            'draggable' => false,
            'text' => '',
            'book_aside' => [],
            'updated_at' => $translate->get(['date', $this->get('url')], $lang, function($date) {
                return substr($date, 0, 4);
            })
        )); ?>
        <p>&nbsp;</p>
        <section class="indent el_form">
            <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl('book/recite/import', \Defines\Extension::JSON) ?>">
                <input type="hidden" name="action" value="manual" />
                <input type="hidden" required name="isbn" value="<?php echo $translate->get(['isbn', $this->get('url')], $lang) ?>" />
                <p><?php echo $translate->sys('LB_BOOK_RECITE_SINGLE') ?>:</p>
                <textarea name="quote"></textarea>
                <p class="el_grid">
                    <span><?php echo $translate->sys('LB_BOOK_PAGE') ?>:</span>
                    <span><input autocomplete="off" type="text" required name="page" /></span>
                    <span>&nbsp;</span>
                    <span>&nbsp;</span>
                    <span>&nbsp;</span>
                    <span>&nbsp;</span>
                    <span><input class="bg_normal" type="submit" value="<?php echo $translate->sys('LB_BUTTON_CREATE') ?>" /></span>
                </p>
            </form>
        </section>
        <?php
        if (!$this->get('list')):
            echo $translate->sys('LB_CONTENT_IS_MISSING');
        else:
            /* @var $el \Data\Doctrine\Main\Content */
            foreach ($this->get('list') as $el):
                $isOwn = $el->getAuthor() === \System\Registry::user()->getEntity();
                ?><div class="el_table nowrap indent cr_pointer<?php if ($isOwn): ?> ui" data-class="Modules/Book/Overview/Quote" data-actions="quote" data-id="<?php echo $el->getId(); endif; ?>">
                    <div class="nowrap bg_mask indent indent_neg_left left w-mm-100 ui-submit">
                        <?php
                        if ($isOwn):
                            ?><img width="16px" height="16px" align="absmiddle" src="<?php echo (new \System\Minify\Images)->get() ?>css/el_box/write.gif" /><?php
                        endif;

                        echo substr($el->getPattern(), strlen($this->get('entity')->getPattern()) + 1) . ' ' . $translate->sys('LB_PAGE');

                        if ($el->getAccess() === \Defines\User\Access::getModDecline()):
                            ?><span title="<?php echo $translate->sys('LB_CONTENT_WAS_REJECTED') ?>">[X]</span><?php
                        elseif ($el->getAccess() !== \Defines\User\Access::getModApprove()):
                            ?><sup title="<?php echo $translate->sys('LB_PERSON_SUBMIT_PUBLIC') ?>">*</sup><?php
                        endif;
                        ?>
                    </div>
                    <p class="clear-nowrap indent indent_neg_inline ui-target"><?php echo $el->getContent() ?><p>
                </div><?php
            endforeach;
        endif;

        ?>
    </section>
</article>