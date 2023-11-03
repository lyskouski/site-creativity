<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
?>
<article class="el_content">
    <?php
    echo $this->partial(
        'Basic/title', 
        array(
            'title' => $translate->sys('LB_PERSON_ACCOUNTS_LIST'),
            'title_href' => $this->getUrl('person/accounts'),
            'languages' => \Defines\Language::getList()
        )
    );
    ?>
    <section class="indent">
        <p class="indent"><?php echo $translate->sys('LB_PERSON_ACCOUNTS_INFO') ?></p>
    </section>
    <?php 
    foreach ($this->get('accounts', array()) as $aCurrent):
        echo $this->partial('Basic/notion', $aCurrent);
    endforeach;
    ?>
</article>