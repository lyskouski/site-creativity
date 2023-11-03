<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$lang = $translate->getTargetLanguage();
$betaRelease = (int)substr($this->get('alpha'), 8);
$idRelease = (int)substr($this->get('beta'), 8);
/* @var $release \Data\Doctrine\Main\Released */
$release = null;
?>
<article class="el_content">
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_SITE_ROLLOUT'),
        'title_href' => $this->getUrl('dev/rollout'),
        'languages' => \Defines\Language::getList()
    ));
    ?>
    <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl('dev/rollout', \Defines\Extension::JSON) ?>">
    <ul class="list">
        <li>
            <div class="button bg_accepted">
                <span class="el_counter">alpha.creativity.by</span>
                <?php echo str_replace("\n", '<br />', $this->get('alpha')) ?>
            </div>
            <?php
                /* @var $o \Data\Doctrine\Main\Released */
                $type = 'co_attention';
                $icon = '&Cross;';
                $title = $translate->sys('LB_ROLLOUT_TO');
                foreach ($this->get('hg') as $i => $o):
                    if (strpos($o->getVersion(), $this->get('version'))):
                        $type = 'co_approve';
                        $icon = '&check;';
                        $title = $translate->sys('LB_ROLLOUT_BACK');
                        if (is_null($release)):
                            $release = $o;
                        endif;
                    endif;
                    ?><div class="el_grid"><div class="indent el_table el_grid">
                        <?php if (!$i): ?>
                        <aside>
                            <div data-type="submit" data-extra="action=clear" class="right button bg_attention" title="<?php echo $translate->sys('LB_BUTTON_CLEAR_UPDATES') ?>">&cross;</div>
                            <?php if (!$this->get('local')): ?>
                            <div data-type="submit" data-extra="action=check" class="right button bg_note" title="<?php echo $translate->sys('LB_BUTTON_CHECK_UPDATES') ?>">&orarr;</div>
                            <?php endif ?>
                        </aside>
                        <?php endif ?>
                        <span class="indent left"><?php echo $icon ?></span>
                        <span data-type="submit" data-extra="action=alpha&revision=<?php echo $o->getVersion() ?>" class="w-150 button left <?php echo $type ?>" title="<?php echo $title ?>">
                            <?php echo $o->getVersion() ?>
                        </span>
                        <span title="<?php echo $o->getDescription() ?>">&nbsp;<strong><?php
                            if ($o->getContent()):
                                $c = $o->getContent()->getContent();
                                $url = $this->getUrl($o->getContent()->getPattern());
                                echo preg_replace(
                                    '/(#)(\d{1,})/',
                                    "<a class=\"co_attention\" target=\"_blank\" title=\"{$c}\" href=\"{$url}\">$0</a>",
                                    $o->getDescription()
                                );
                            else:
                                echo $o->getDescription();
                            endif;
                            ?></strong>
                            <br />&nbsp;<small><?php echo (new System\Converter\DateDiff)->getInterval($o->getUpdatedAt()->diff(new \DateTime)) ?></small>
                        </span>
                    </div></div><?php
                endforeach;
                ?>
            </div>
        </li>
    </ul>
    </form>
    <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl('dev/rollout', \Defines\Extension::JSON) ?>">
    <ul class="list">
        <li>
            <div class="button bg_button">
                <span class="el_counter">beta.creativity.by</span>
                <?php echo str_replace("\n", '<br />', $this->get('beta')) ?>
            </div>
            <?php
            $format = \Defines\Database\Params::TIMESTAMP;
            // Failed tests
            if ($this->get('phpunit')):
                ?><div class="el_grid"><div class="indent el_table">
                    <div class="right button bg_attention" data-type="submit" data-extra="action=test"><?php echo $translate->sys('LB_BUTTON_RESTART') ?></div>
                    <?php echo $translate->sys('LB_ROLLOUT_FAILED_TESTS') ?>
                </div></div><?php
                foreach ((array)$this->get('phpunit') as $error):
                    ?><div class="el_grid"><div class="indent el_table">
                        <h4><?php echo $error['test'] ?></h4>
                        <?php echo $error['message'] ?>
                    </div></div><?php
                endforeach;
            // Run tests
            else:
                $notTested = is_null($release) || !$release->getTested();
                ?><div class="el_grid">
                    <div class="indent el_table">
                        <div class="button bg_accepted" data-type="submit" data-extra="action=beta"><?php echo $translate->sys('LB_BUTTON_APPLY') ?></div>
                        <p>&nbsp;</p>
                    </div>
                    <div class="indent el_table">
                        <?php if ($notTested): ?>
                            <p class="co_attention"><?php echo $translate->sys('LB_ROLLOUT_DEPRECATED_TESTS'), ' ', $release->getUpdatedAt()->format(Defines\Database\Params::TIMESTAMP) ?></p>
                            <br />
                        <?php endif ?>
                        <div class="button bg_attention" data-type="submit" data-extra="action=test"><?php echo $translate->sys('LB_BUTTON_RESTART') ?></div>
                    </div>
                </div><?php
            endif;
            ?>
            </div>
        </li>
    </ul>
    </form>
    <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl('dev/rollout', \Defines\Extension::JSON) ?>">
        <ul class="list">
            <li>
                <div class="button bg_attention">
                    <span class="el_counter">creativity.by</span>
                    <?php echo str_replace("\n", '<br />', $this->get('live')) ?>
                </div>
                <?php
                if ($idRelease == $betaRelease):
                    ?><div class="indent">
                        <?php echo $translate->sys('LB_SITE_ROLLOUT_NEW') ?>:
                        <input type="text" size="4" name="live" value="<?php echo $this->get('last') ?>" />
                        <input type="text" name="title" value="" />
                        <div class="button bg_accepted" data-type="submit" data-extra="action=release"><?php echo $translate->sys('LB_BUTTON_CREATE') ?></div>
                        <p>&nbsp;</p>
                    </div><?php
                endif;
                ?>
            </li>
        </ul>
    </form>
    <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl('dev/rollout', \Defines\Extension::JSON) ?>">
        <ul class="list">
            <?php
            /* @var $live \Data\Doctrine\Main\ReleasedLive */
            foreach ($this->get('live_list') as $live):
                ?><li>
                    <div class="left button <?php echo $live->getActive() ? 'bg_note' : 'bg_attention" data-type="submit" data-extra="action=live&live='. $live->getId() ?>">
                        <?php
                        echo $live->getVersion();
                        if ($live->getActive()):
                            echo ' (current)';
                        endif;
                        ?>
                    </div>
                    <div class="indent">&nbsp;<?php echo $live->getDescription(), ' (', $live->getReleased()->getVersion(), ')' ?></div>
                </li><?php
            endforeach;
            ?>
        </ul>
    </form>
</article>