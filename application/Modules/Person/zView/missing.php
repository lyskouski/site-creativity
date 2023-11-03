<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
?>
<article class="el_content">
    <?php echo $this->partial('card') ?>
    <section class="indent">
        <p><?php echo $translate->sys('LB_ERROR_PERSON_NOT_TRANSLATED') ?></p>
        <p>&nbsp;</p>
        <ul class="list">
            <?php foreach ($this->get('languages') as $sLang): ?>
            <li>
                <a class="im_left button" href="<?php echo $this->getUrl($this->get('url'), null, $sLang)?>">
                    <span class="im_lang im_lang_<?php echo $sLang ?>">&nbsp;</span>
                    <?php echo $translate->sys('LB_PERSON_PAGE'), ' ' , $this->get('username'); ?>
                    (<?php echo $translate->sys('LB_LANG_' . strtoupper($sLang), $sLang) ?>)
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </section>
</article>