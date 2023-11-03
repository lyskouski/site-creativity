<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$sModuleUrl = (new \Engine\Request\Params)->getModuleUrl();
?>
<article class="el_grid">
    <?php echo $this->partial('Basic/Nav/vertical') ?>

    <section class="el_content">
        <?php
        echo $this->partial('Basic/title', array(
            'subtitle' => $translate->sys('LB_ACCESS_TASKS_AUDITOR')
        ));
        ?>

        <div class="el_table indent">
            <aside>
                <?php
                $i = 0;
                foreach ($this->get('text_status', array()) as $sLang => $iCount):
                    if ($i % 3 === 0):
                        ?><p class="el_grid"><?php
                    endif;
                    if ($iCount > 0):
                        ?><a href="<?php echo $this->getUrl('dev/tasks/auditor/text', null, $sLang) ?>" class="im_left">
                            <span class="im_lang im_lang_<?php echo $sLang ?>">&nbsp;</span>
                            <?php echo $iCount ?>
                        </a><?php
                    else:
                        ?><span class="im_left">
                            <span class="im_lang im_lang_<?php echo $sLang ?>">&nbsp;</span>
                            <?php echo $translate->sys('LB_NO') ?>
                        </span><?php
                    endif;
                    $i++;
                    if ($i % 3 === 0):
                        ?></p><?php
                    endif;
                endforeach;
                ?>
            </aside>
            <header>
                <a href="<?php echo $this->getUrl('dev/tasks/auditor/text') ?>"><?php echo $translate->sys('LB_CONTENT_FOR_APPROVEMENT') ?></a>
            </header>
            <p><?php echo $translate->sys('LB_CONTENT_FOR_APPROVEMENT_DESC') ?></p>
        </div>

        <?php if (\System\Registry::user()->isAdmin()): ?>
        <?php
        echo $this->partial('Basic/title', array(
            'subtitle' => $translate->sys('LB_ACCESS_ADMIN')
        ));
        ?>
        <div class="indent">
            <div class="indent el_form el_border">
                <header>
                    <strong><?php echo $translate->sys('LB_CONTENT_WAS_REJECTED') ?></strong>
                </header>
                <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl('dev/tasks/auditor', \Defines\Extension::JSON) ?>">
                    <input type="hidden" name="action" value="restart" />
                    <p class="el_grid">
                        <span><?php echo $translate->sys('LB_CONTENT_PATTERN') ?>:</span>
                        <input type="text" name="pattern" value="" placeholder="<?php echo $translate->sys('LB_CONTENT_PATTERN') ?>..." />
                    </p>
                    <input class="bg_button" type="submit" value="<?php echo $translate->sys('LB_BUTTON_RESTART') ?>" />
                </form>
            </div>
        </div>
        <p>&nbsp;</p>
        <div class="indent">
            <div class="indent el_form el_border">
                <header class="co_attention">
                    <strong><?php echo $translate->sys('LB_CONTENT_FOR_DELETE') ?></strong>
                </header>
                <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl('dev/tasks/auditor', \Defines\Extension::JSON) ?>">
                    <input type="hidden" name="action" value="delete" />
                    <p class="el_grid">
                        <span><?php echo $translate->sys('LB_CONTENT_PATTERN') ?>:</span>
                        <input type="text" name="pattern" value="" placeholder="<?php echo $translate->sys('LB_CONTENT_PATTERN') ?>..." />
                    </p>
                    <input class="bg_attention" type="submit" value="<?php echo $translate->sys('LB_BUTTON_DELETE') ?>" />
                </form>
            </div>
        </div>
        <?php endif ?>

    </section>
</article>