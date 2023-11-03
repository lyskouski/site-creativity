<?php
/* @var $this \Engine\Response\Template */

$translate = \System\Registry::translation();

?>
<article class="el_content">
    <?php echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_SITE_INFO'),
        'title_href' => $this->getUrl('info')
    ));
    ?>

    <section class="indent">
        <p><?php echo $translate->sys('LB_COPYRIGHT_CONDITIONS') ?></p>
        <p class="clear">&nbsp;</p>

        <p>
            <a class="co_attention" href="<?php echo $this->getUrl('info/policy') ?>"><?php echo $translate->sys('LB_SITE_PRIVACY_POLICY') ?></a>:
            <?php echo $translate->get(['description', 'info/policy']) ?>
        </p>
        <p class="clear">&nbsp;</p>

        <p>
            <a class="co_attention" href="<?php echo $this->getUrl('info/terms') ?>"><?php echo $translate->sys('LB_SITE_TERMS') ?></a>:
            <?php echo $translate->get(['description', 'info/terms']) ?>
        </p>
        <p class="clear">&nbsp;</p>

        <p>
            <a class="co_attention" href="<?php echo $this->getUrl('about') ?>"><?php echo $translate->sys('LB_SITE_ABOUT') ?></a>:
            <?php echo $translate->get(['description', 'about']) ?>
        </p>
        <p class="clear">&nbsp;</p>

        <?php echo $this->partial($this->get('workflow_tmp'), $this->get('workflow')) ?>
    </section>

    <p class="clear">&nbsp;</p>

    <section>
        <?php
        echo $this->partial('Basic/title', array(
            'subtitle' => $translate->sys('LB_SITE_DECOR'),
            'subtitle_href' => $this->getUrl('info/decor')
        ));
        ?>
        <p class="indent">
            <?php echo $translate->get(['description', 'info/decor']) ?><br />
            <a class="co_attention" href="<?php echo $this->getUrl('info/decor') ?>"><?php echo $translate->sys('LB_GET_DETAILS') ?></a>
        </p>
    </section>
</article>