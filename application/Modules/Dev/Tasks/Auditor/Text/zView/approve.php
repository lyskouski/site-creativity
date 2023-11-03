<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$sActive = $this->get('url_active');

echo $this->partial('Basic/Nav/crumbs');

$aData = $this->get('list');
/* @var $o \Data\Doctrine\Main\ContentNew */
$o = current($aData);
$username = \Data\UserHelper::getUsername($o->getAuthor());

$sActualUrl = $this->getUrl($o->getPattern(), null, $o->getLanguage());
?>
<article class="el_content">
    <p>&nbsp;</p>
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_ACCESS_TASKS_AUDITOR'),
        'title_href' => '',
        'subtitle' => $translate->sys('LB_VIEW_PAGE'),
        'subtitle_href' => $sActualUrl
    ));
    ?>

    <section class="clear">
        <div class="indent bg_form">
            <?php
            if (\System\Registry::user()->checkAccess('dev/tasks/auditor/text', 'author')):
                ?>
                <form method="POST" action="<?php echo $this->getUrl('dev/tasks/auditor/text', \Defines\Extension::JSON) ?>" class="right ui" data-class="Request/Form" data-actions="init">
                    <input type="hidden" name="action" value="author" />
                    <input type="text" name="username" value="<?php echo $username ?>" />
                    <input class="button bg_attention" type="submit" value="<?php echo $translate->sys('LB_BUTTON_MODIFY') ?>" />
                </form>
                <?php
            endif;
            ?>
            <?php echo $translate->sys('LB_PAGE_CONTENT')?>: <a href="<?php echo $sActualUrl ?>"><?php echo $sActualUrl ?></a>
            (<?php echo $translate->sys('LB_ACCESS_AUTHOR') ?>:
            <a href="<?php echo $this->getUrl('person/' . $username) ?>"><?php echo $username ?></a>)
        </div>
    </section>
    <section class="indent">
        <p>&nbsp;</p>
        <?php foreach ($aData as $o): ?>
            <div class="el_border indent el_panel">
                <header class="<?php echo $o->getType() !== 'og:reply' ? 'bg_mask' : 'bg_attention' ?>"><?php echo $o->getType() ?></header>
                <?php
                $a = explode('#', $o->getType());
                $sType = $a[0];
                if (in_array($a[0], array('image', 'og:image'), true)):
                    ?><img src="<?php echo $o->getContent() ?>" /><?php
                elseif ($a[0] === 'content' && strpos($o->getPattern(), 'book/series') !== false):
                    echo $this->partial('Entity/Book/Series/list', array(
                        'list' => explode(',', $o->getContent())
                    ));
                else:
                    echo $o->getContent();
                endif;
                ?>
            </div>
        <?php endforeach ?>

        <form method="POST" action="<?php echo $this->getUrl('dev/tasks/auditor/text', \Defines\Extension::JSON) ?>" class="ui" data-class="Request/Form" data-actions="init">
            <textarea name="reply" placeholder="<?php echo $translate->sys('LB_PERSON_SUBMIT_REJECTED') ?>..."></textarea>
            <input class="bg_normal left" type="submit" data-extra="action=approve" value="<?php echo $translate->sys('LB_BUTTON_APPROVE') ?>" />
            <input type="checkbox" name="next" <?php echo $this->get('next') ? 'checked' : '' ?> /> <?php echo $translate->sys('LB_TASK_AUTO_NEW') ?>
            <input class="bg_attention" type="submit" data-extra="action=reject" value="<?php echo $translate->sys('LB_BUTTON_REJECT') ?>" />
            <?php if (\System\Registry::user()->checkAccess('dev/tasks/auditor/text', 'delete')): ?>
            <input class="bg_attention" type="submit" data-extra="action=delete" value="<?php echo $translate->sys('LB_BUTTON_DELETE') ?>" />
            <?php endif ?>
        </form>
    </section>
    <p>&nbsp;</p>
</article>