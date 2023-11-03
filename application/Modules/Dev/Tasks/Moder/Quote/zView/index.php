<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$sActive = $this->get('url_active');

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content el_table_pair">
    <p>&nbsp;</p>
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_TASK_MODER_QUOTE'),
        'title_href' => $this->getUrl('dev/tasks/moder/quote'),
        'languages' => \Defines\Language::getList()
    ));

    $sTopicUrl = '';

    $jsonUrl = $this->getUrl($this->get('url'), \Defines\Extension::JSON);

    /* @var $o \Data\Doctrine\Main\Content */
    foreach ($this->get('list') as $o):
        $sLogin = \Data\UserHelper::getUsername($o->getAuthor());
        $params = explode('/', $o->getPattern());
        $page = end($params);
        $url = implode('/', array_slice($params, 0, -1));
        if ($url !== $sTopicUrl):
            ?><h3 class="indent"><a href="<?php echo $this->getUrl($o->getPattern())?>"><?php echo $translate->get(['og:title', $url]) ?></a></h3><?php
            $sTopicUrl = $url;
        endif;
        ?><section class="el_table indent ui" data-class="Modules/Book/Overview/Quote" data-actions="quote" data-id="<?php echo $o->getId() ?>">
            <header>
                <small class="right">
                    <?php echo $o->getUpdatedAt()->format(\Defines\Database\Params::TIMESTAMP) ?>
                    <?php
                    if ($o->getAuditor()):
                        $sAuditor = $o->getAuditor()->getUsername();
                        ?><a href="<?php echo $this->getUrl("person/$sAuditor") ?>"><?php echo $sAuditor ?></a>
                        <span class="right">(<a href="<?php echo $this->getUrl('dev/history/' . $o->getId() ) ?>"><?php
                            echo $translate->sys('LB_FORUM_HISTORY')
                        ?></a>)</span><?php
                    endif;
                    ?>
                    <br />
                    <?php if (\System\Registry::user()->checkAccess($this->get('url'), 'delete')): ?>
                    <a class="right button bg_note cr_pointer ui" data-class="Request/Pjax" data-actions="init" data-href="<?php echo $jsonUrl ?>" data-data="{'action':'delete', 'id':<?php echo $o->getId() ?>}"><?php echo $translate->sys('LB_BUTTON_DELETE') ?></a>
                    <?php endif; ?>
                    <a class="right button bg_attention cr_pointer ui" data-class="Request/Pjax" data-actions="init" data-href="<?php echo $jsonUrl ?>" data-data="{'action':'decline', 'id':<?php echo $o->getId() ?>}"><?php echo $translate->sys('LB_BUTTON_DECLINE') ?></a>
                    <a class="right button bg_normal cr_pointer ui" data-class="Request/Pjax" data-actions="init" data-href="<?php echo $jsonUrl ?>" data-data="{'action':'approve', 'id':<?php echo $o->getId() ?>}"><?php echo $translate->sys('LB_BUTTON_APPROVE') ?></a>
                </small>
                <?php
                if (\System\Registry::user()->checkAccess($this->get('url'), 'edit')):
                    ?><a class="right cr_pointer ui-submit" title="<?php echo $translate->sys('LB_BUTTON_MODIFY') ?>">
                        <img width="16px" height="16px" src="<?php echo (new \System\Minify\Images)->get() ?>css/el_box/write.gif" />&nbsp;
                    </a><?php
                endif;
                ?>
                <a href="<?php echo $this->getUrl("person/$sLogin") ?>"><?php echo $sLogin ?></a>
            </header>
            <p class="clear_mrg_right">[<?php echo $translate->sys('LB_PAGE'), ' ', $page ?>] <span class="ui-target"><?php echo nl2br(htmlspecialchars($o->getContent())) ?></span></p>
        </section><?php
    endforeach;
?>
</article>