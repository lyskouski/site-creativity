<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$aTypes = \Defines\User\Account::getTextList();
$imgPath = new \System\Minify\Images();
?>
<article class="el_content">

    <?php
    echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_SITE_ABOUT'),
        'title_href' => $this->getUrl('about'),
        'subtitle' => $translate->sys('LB_ACCESS_ADMIN'),
        'languages' => \Defines\Language::getList()
    ));
    ?>
    <section class="indent">
        <a href="<?php echo $this->getUrl('person/FieryCat') ?>"><h3>FieryCat</h3></a>
        <p><?php echo $translate->get(['description', 'person/FieryCat']) ?></p>
        <footer class="el_footer">
            <div class="menu">
            <?php
            /* @var $oAccount \Data\Doctrine\Main\UserAccount */
            foreach ($this->get('FieryCat', array()) as $oAccount):
                $sLink = \Defines\Response\AccountLinks::get($oAccount->getType(), $oAccount->getAccount());
                if ($sLink):
                    ?><a href="<?php echo $sLink ?>" target="_blank" class="el_width_auto active"><?php
                else:
                    ?><a href="<?php echo $this->getUrl('index') ?>" class="inactive el_width_auto"><?php
                endif;
                $imgUrl = $imgPath->adaptAccount($oAccount->getType(), '_type');
                ?><img width="14px" height="12px" class="left" src="<?php echo $imgUrl ?>" /><?php
                echo $aTypes[$oAccount->getType()], '&nbsp;';
                ?></a><?php
            endforeach;
            ?>
            </div>
        </footer>
        <p>&nbsp;</p>
    </section>
    <p>&nbsp;</p>
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_SITE_ACCESS'),
        'num' => 2
    ));
    ?>
    <section class="indent">
        <?php
            $aData = $this->get('list');

            // temporary function for a recursion functionality
            $fCircle = function($id, $aAccess) use ($aData) {
                ?><ul class="list">
                    <li>
                        <!-- div class="button bg_none" -->
                        <?php
                        if ($aAccess['sub']):
                            ?><span class="button cr_pointer ui left" data-class="View/Height" data-actions="spoiler" data-target="closest:li: > ul" data-status="1" data-invert="&searr;&nbsp;">&nwarr;&nbsp;</span><?php
                        endif;
                        ?><b class="button bg_none cr_default">&nbsp;<?php echo $aAccess['title'] ?></b>
                        <p class="indent bg_mask"><small><?php echo $aAccess['desc'] ?></small></p><?php
                        if ($aAccess['sub']):
                            foreach ($aAccess['sub'] as $idChild):
                                $this->evalFunction('circle', array($idChild, $aData[$idChild]));
                            endforeach;
                        endif; ?>
                        <!-- /div -->
                    </li>
                </ul><?php
            };
            $this->regFunction('circle', $fCircle);

            foreach ($aData as $id => $aAccess):
                if ($aAccess['child']):
                    continue;
                endif;
                $fCircle($id, $aAccess);
            endforeach;
            ?>
    </section>
    <p>&nbsp;</p>
    <section class="indent">
        <a class="button bg_normal" href="<?php echo $this->getUrl('info/policy') ?>"><?php echo $translate->sys('LB_SITE_PRIVACY_POLICY') ?></a>
        <a class="button bg_normal" href="<?php echo $this->getUrl('info/terms') ?>"><?php echo $translate->sys('LB_SITE_TERMS') ?></a>
        <a class="button bg_attention" href="<?php echo $this->getUrl('index') ?>"><?php echo $translate->sys('LB_SITE_RETURN2MAIN') ?></a>
    </section>
</article>