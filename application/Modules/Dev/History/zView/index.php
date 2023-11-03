<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

$em = (new \Data\ContentHelper)->getEntityManager();

$sContent = $this->get('current')->getContent();
$sAuthor = \Data\UserHelper::getUsername($this->get('current')->getAuthor());
$sTime = $this->get('current')->getUpdatedAt()->format(\Defines\Database\Params::TIMESTAMP);
$sRefUrl = (new \Engine\Request\Input)->getRefererUrl();
?>
<article class="el_content">
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_FORUM_HISTORY_TITLE'),
        'subtitle' => $translate->sys('LB_RETURN'),
        'subtitle_href' => $sRefUrl
    ));
    ?>
    <section class="indent el_table">
        <aside>
            <a href="<?php echo $this->getUrl("person/$sAuthor") ?>"><?php echo $sAuthor ?></a><br />
            <?php echo $this->get('current')->getUpdatedAt()->format(\Defines\Database\Params::TIMESTAMP) ?><br />
            <?php echo $this->partial('Basic/Desc/access', array('value' => $this->get('current')->getAccess())) ?>
        </aside>
        <header>
            <a href="<?php echo $this->getUrl($this->get('current')->getPattern()) ?>"><?php echo $translate->get(['og:title', $this->get('current')->getPattern()]) ?></a>
        </header>
        <p><?php echo nl2br($sContent) ?></p>
    </section>
    <p>&nbsp;</p>
    <section class="el_harmonic ui" data-class="Ui/Element" data-actions="harmonic">
        <?php
        /* @var $oContent \Data\Doctrine\Main\ContentHistory */
        foreach ($this->get('list') as $i => $oContent):
            ?>
            <section class="el_table indent <?php echo $i ? '' : 'active' ?>">
                <header>
                    <?php
                    if ($oContent->getAuditorId()):
                        $iAuditor = $oContent->getAuditorId();
                    else:
                        $iAuditor = $oContent->getAuthorId();
                    endif;
                    $sAuditor = 'Anonimus';
                    if ($iAuditor):
                        $oUser = $em->find(\Defines\Database\CrMain::USER, $iAuditor);
                        if ($oUser):
                            $sAuditor = $oUser->getUsername();
                        endif;
                    endif;
                    ?>
                    <span class="right"><?php echo $this->partial('Basic/Desc/access', array('value' => $oContent->getAccess())) ?></span>
                    <span>
                        <?php echo $translate->sys('LB_HISTORY_CHANGES') ?>
                        <a class="nowrap ui-target" href="<?php echo $this->getUrl("person/$sAuditor") ?>"><?php echo $sAuditor ?></a>
                    </span>
                    <small><?php
                        echo $oContent->getUpdatedAt()->format(\Defines\Database\Params::TIMESTAMP), ' &raquo; ', $sTime;
                        $sTime = $oContent->getUpdatedAt()->format(\Defines\Database\Params::TIMESTAMP);
                    ?></small>
                </header>
                <p><?php
                    $opcodes = \System\Converter\FineDiff::getDiffOpcodes($oContent->getContent(), $sContent, array(\System\Converter\FineDiff::wordDelimiters));
                    echo nl2br(\System\Converter\FineDiff::renderDiffToHTMLFromOpcodes($oContent->getContent(), $opcodes));
                    $sContent = $oContent->getContent();
                ?></p>
            </section>
            <?php
        endforeach;
        ?>
    </section>
</article>