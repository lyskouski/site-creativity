<?php
/* @var $this \Engine\Response\Template */

$translate = \System\Registry::translation();
?>
<section class="el_harmonic ui" data-class="Ui/Element" data-actions="harmonic">
    <?php
    /* @var $oContent \Data\Doctrine\Main\Content */
    foreach ($this->get('list') as $i => $oContent):
        /* @var $oDiff \DateInterval */
        $oDiff = $oContent->getUpdatedAt()->diff(new \DateTime);
        ?>
    <section class="el_table indent cr_pointer <?php echo $i ? '' : 'active' ?> ui" data-class="View/Href" data-actions="target" data-target=".ui-target">
        <?php if ($oDiff->format('%y') == 0 && $oDiff->format('%m') == 0 && $oDiff->format('%d') < 7): ?>
        <aside>
            <div class="el_new">&nbsp;</div>
            <p>&nbsp;</p>
        </aside>
        <?php endif ?>
        <header>
            <a class="nowrap ui-target" href="<?php echo $this->getUrl($oContent->getPattern()) ?>"><?php echo $oContent->getContent() ?></a>
            <small><?php echo $oContent->getUpdatedAt()->format(\Defines\Database\Params::TIMESTAMP) ?></small>
        </header>
        <p><?php echo $translate->get(['description', $oContent->getPattern()]); ?></p>
    </section>
    <?php endforeach ?>
</section>