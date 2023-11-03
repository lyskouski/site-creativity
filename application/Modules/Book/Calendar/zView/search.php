<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

?>
<section>
    <search id="search_list">
        <p class="indent">&nbsp;</p>
        <?php
        if (!$this->get('list')):
            ?><p class="indent co_attention"><?php echo $translate->sys('LB_ERROR_BOOK_MISSING') ?></p><p class="indent">&nbsp;</p><?php
        endif;

        foreach ($this->get('list') as $data):
            echo $this->partial('Basic/notion', $data);
        endforeach;
        ?>
    </search>
</section>