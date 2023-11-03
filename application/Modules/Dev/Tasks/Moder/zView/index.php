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
            'subtitle' => $translate->sys('LB_ACCESS_TASKS_MODER')
        ));
        ?>

        <div class="el_table indent">
            <aside>
                <?php
                $i = 0;
                foreach ($this->get('topics', array()) as $sLang => $iCount):
                    if ($i % 3 === 0):
                        ?><p class="el_grid"><?php
                    endif;

                    if ($iCount > 0):
                        ?><a href="<?php echo $this->getUrl("dev/tasks/moder/topics/{$sLang}", null, $sLang) ?>" class="im_left">
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
                <a href="<?php echo $this->getUrl('dev/tasks/moder/topics') ?>"><?php echo $translate->sys('LB_TASK_MODER_TOPICS') ?></a>
            </header>
            <p><?php echo $translate->sys('LB_TASK_MODER_TOPICS_DESC') ?></p>
        </div>

        <div class="el_table indent">
            <aside>
                <?php
                $i = 0;
                foreach ($this->get('comments', array()) as $sLang => $iCount):
                    if ($i % 3 === 0):
                        ?><p class="el_grid"><?php
                    endif;

                    if ($iCount > 0):
                        ?><a href="<?php echo $this->getUrl("dev/tasks/moder/comments/{$sLang}", null, $sLang) ?>" class="im_left">
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
                <a href="<?php echo $this->getUrl('dev/tasks/moder/comments') ?>"><?php echo $translate->sys('LB_TASK_MODER_COMMENTS') ?></a>
            </header>
            <p><?php echo $translate->sys('LB_TASK_MODER_COMMENTS_DESC') ?></p>
        </div>

        <div class="el_table indent">
            <aside>
                <?php
                $i = 0;
                foreach ($this->get('reply', array()) as $sLang => $iCount):
                    if ($i % 3 === 0):
                        ?><p class="el_grid"><?php
                        endif;

                        if ($iCount > 0):
                            ?><a href="<?php echo $this->getUrl("dev/tasks/moder/reply/{$sLang}", null, $sLang) ?>" class="im_left">
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
                <a href="<?php echo $this->getUrl('dev/tasks/moder/reply') ?>"><?php echo $translate->sys('LB_TASK_MODER_REPLY') ?></a>
            </header>
            <p><?php echo $translate->sys('LB_TASK_MODER_REPLY_DESC') ?></p>
        </div>

        <div class="el_table indent">
            <aside>
                <?php
                $i = 0;
                foreach ($this->get('quote', array()) as $sLang => $iCount):
                    if ($i % 3 === 0):
                        ?><p class="el_grid"><?php
                        endif;

                        if ($iCount > 0):
                            ?><a href="<?php echo $this->getUrl("dev/tasks/moder/quote/{$sLang}", null, $sLang) ?>" class="im_left">
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
                <a href="<?php echo $this->getUrl('dev/tasks/moder/quote') ?>"><?php echo $translate->sys('LB_TASK_MODER_QUOTE') ?></a>
            </header>
            <p><?php echo $translate->sys('LB_TASK_MODER_QUOTE_DESC') ?></p>
        </div>
    </section>

</article>