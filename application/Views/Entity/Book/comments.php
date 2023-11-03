<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

$entity = $translate->entity(['og:title', $this->get('url')]);
$lang = $entity->getLanguage();

$aComents = $this->get('list', array());
$sUrl = $this->getUrl($this->get('url'));
$oValid = new \Access\Validate\Check();

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content" itemprop="mainEntity">
    <?php echo $this->partial('Entity/Book/nav_list', array(
       'url' => $this->get('url'),
       'url_active' => $this->get('url') . '/comment'
    )) ?>

    <header class="el_content_header">
        <h1 class="title"><a class="nowrap" href="<?php echo $sUrl ?>"><?php echo $translate->sys('LB_COMMENT') ?></a></h1>
    </header>

    <section class="indent clear">
        <?php echo $this->partial('Basic/notion_plain', array(
            'author_txt' => $translate->get(['author', $this->get('url')], $lang),
            'title' => $entity->getContent(),
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
    </section>

    <div class="indent">
        <?php echo $this->partial('Entity/share', array('class' => 'left')) ?>
        <p class="indent">&nbsp;</p>
    </div>

    <?php if ($oValid->setType(\Defines\User\Access::MARK)->isAccepted()): ?>
    <section class="indent el_form clear">
        <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl( $this->get('url'), \Defines\Extension::JSON ) ?>">
            <input type="hidden" name="action" value="comment" />
            <p class="el_grid">
                <span class="el_table_newline indent"><?php echo $translate->sys( 'LB_COMMENT_MARK' ) ?>:</span>
                <span class="el_table_newline bg_button">&nbsp;<input class="el_radio_col" type="radio" name="mark" value="votes_up" checked="checked" id="mark-fine" /><label for="mark-fine"><?php echo $translate->sys( 'LB_COMMENT_MARK_POSITIVE' ) ?></label></span>
                <span class="el_table_newline bg_attention">&nbsp;<input class="el_radio_col" type="radio" name="mark" value="votes_down" id="mark-bad" /><label for="mark-bad"><?php echo $translate->sys( 'LB_COMMENT_MARK_NEGATIVE' ) ?></label></span>
                <span>&nbsp;<input class="el_radio_col" type="radio" name="mark" value="" id="mark-skip" /><label for="mark-skip"><?php echo $translate->sys( 'LB_COMMENT_MARK_SKIP' ) ?></label></span>
            </p>
            <textarea name="content" required placeholder="<?php echo $translate->sys( 'LB_COMMENT_CONTENT' ) ?>"></textarea>
            <input class="bg_normal" type="submit" value="<?php echo $translate->sys( 'LB_FORUM_CREATE' ) ?>">
        </form>
    </section>
    <?php else: ?>
        <p class="co_attention indent">
            <strong><?php echo $translate->sys('LB_USER_ACCESS_COMMENT') ?>:</strong>&nbsp;
            <?php
            if (\System\Registry::user()->isLogged()):
                echo $translate->sys('LB_HEADER_423');
            else:
                echo $translate->sys('LB_ERROR_NOT_AUTHORIZED');
            endif;
            ?>
        </p>
    <?php endif ?>
    <p>&nbsp;</p>
    <?php
    $i = 0;
    /* @var $oComment \Data\Doctrine\Main\Content */
    foreach ($aComents as $i => $oComment):
        echo $this->partial('Entity/Basic/comment', array(
            'comment' => $oComment
        ));
    endforeach;

    if ($i === \Defines\Database\Params::COMMENTS_ON_PAGE):
        ?><aside class="indent center">
            <p>&nbsp;</p>
            <a class="button bg_form ui" href="<?php echo $sUrl ?>"  data-class="Request/Pjax" data-actions="init" data-data="{'action':'comment','page':<?php echo $this->get('page', 1) ?>}">
                <?php echo $translate->sys('LB_COMMENT_MORE') ?>
            </a>
        </aside><?php
    endif;
    ?>
</article>