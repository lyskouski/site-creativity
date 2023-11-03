<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$sModuleUrl = (new \Engine\Request\Params)->getModuleUrl();
?>
<article class="el_grid">
    <?php echo $this->partial('Basic/Nav/vertical') ?>


    <section class="el_content">
        <?php echo $this->partial('Basic/title', array(
            'subtitle' => $translate->sys('LB_ACCESS_TASKS_TRANSLATOR')
        )); ?>

        <?php if (\System\Registry::user()->checkAccess($sModuleUrl . '/gui')): ?>
            <div class="el_table indent">
                <aside>
                    <?php
                    $i = 0;
                    foreach ($this->get('gui_status', array()) as $sLang => $aStatus):
                        if ($i%3 === 0):
                            ?><p class="el_grid"><?php
                        endif;
                        ?><a href="<?php echo $this->getUrl("dev/tasks/translation/gui/{$sLang}") ?>" class="im_left">
                            <span class="im_lang im_lang_<?php echo $sLang ?>">&nbsp;</span>
                            <?php echo floor(100 * (1 - $aStatus['missing'] / $aStatus['all'])) ?>%
                        </a>
                        <?php
                        $i++;
                        if ($i%3 === 0):
                            ?></p><?php
                        endif;
                    endforeach;
                    ?>
                </aside>
                <header>
                    <a href="<?php echo $this->getUrl('dev/tasks/translation/gui') ?>"><?php echo $translate->sys('LB_TASK_TRANSLATION_INTERFACE') ?></a>
                </header>
                <p><?php echo $translate->sys('LB_TASK_TRANSLATION_INTERFACE_DESC') ?></p>
            </div>
        <?php else: ?>
            <div class="el_table indent el_form">
                <span class="right bg_attention indent el_back_indent"><?php echo $translate->sys('LB_HEADER_423') ?></span>
                <?php echo $translate->sys('LB_TASK_TRANSLATION_INTERFACE') ?>
            </div>
        <?php endif ?>

        <?php if (\System\Registry::user()->checkAccess($sModuleUrl . '/text')): ?>
            <div class="el_table indent">
                <aside>
                    <?php
                    $i = 0;
                    foreach ($this->get('text_status', array()) as $sLang => $iCount):
                        if ($i%3 === 0):
                            ?><p class="el_grid"><?php
                        endif;
                        if ($iCount > 0):
                            ?><a href="<?php echo $this->getUrl('dev/tasks/translation/text', null, $sLang) ?>" class="im_left">
                                <span class="im_lang im_lang_<?php echo $sLang ?>">&nbsp;</span>
                                <?php echo $iCount ?>
                            </a>
                            <?php
                        else:
                            ?><span class="im_left">
                                <span class="im_lang im_lang_<?php echo $sLang ?>">&nbsp;</span>
                                <?php echo $translate->sys('LB_NO') ?>
                            </span><?php
                        endif;
                        $i++;
                        if ($i%3 === 0):
                            ?></p><?php
                        endif;
                    endforeach;
                    ?>
                </aside>
                <header>
                    <a href="<?php echo $this->getUrl('dev/tasks/translation/text') ?>"><?php echo $translate->sys('LB_TASK_TRANSLATION_TEXT') ?></a>
                </header>
                <p><?php echo $translate->sys('LB_TASK_TRANSLATION_TEXT_DESC') ?></p>
            </div>
        <?php else: ?>
            <div class="el_table indent el_form">
                <span class="right bg_attention indent el_back_indent"><?php echo $translate->sys('LB_HEADER_423') ?></span>
                <?php echo $translate->sys('LB_TASK_TRANSLATION_TEXT') ?>
            </div>
        <?php endif ?>

        <?php if (\System\Registry::user()->checkAccess($sModuleUrl . '/book')): ?>
            <div class="el_table indent">
                <aside>
                    <?php
                    $i = 0;
                    foreach ($this->get('book_status', array()) as $sLang => $iCount):
                        if ($i%3 === 0):
                            ?><p class="el_grid"><?php
                        endif;
                        if ($iCount > 0):
                            ?><a href="<?php echo $this->getUrl('dev/tasks/translation/book', null, $sLang) ?>" class="im_left">
                                <span class="im_lang im_lang_<?php echo $sLang ?>">&nbsp;</span>
                                <?php echo $iCount ?>
                            </a>
                            <?php
                        else:
                            ?><span class="im_left">
                                <span class="im_lang im_lang_<?php echo $sLang ?>">&nbsp;</span>
                                <?php echo $translate->sys('LB_NO') ?>
                            </span><?php
                        endif;
                        $i++;
                        if ($i%3 === 0):
                            ?></p><?php
                        endif;
                    endforeach;
                    ?>
                </aside>
                <header>
                    <a href="<?php echo $this->getUrl('dev/tasks/translation/book') ?>"><?php echo $translate->sys('LB_TASK_TRANSLATION_BOOK') ?></a>
                </header>
                <p><?php echo $translate->sys('LB_TASK_TRANSLATION_BOOK_DESC') ?></p>
            </div>
        <?php else: ?>
            <div class="el_table indent el_form">
                <span class="right bg_attention indent el_back_indent"><?php echo $translate->sys('LB_HEADER_423') ?></span>
                <?php echo $translate->sys('LB_TASK_TRANSLATION_BOOK') ?>
            </div>
        <?php endif ?>
    </section>


</article>