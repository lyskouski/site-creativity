<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$activeCode = $this->get('error');
?>
<article class="el_content">
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $activeCode ? $translate->sys("LB_HEADER_{$activeCode}") : $translate->sys('LB_FORUM_BUGS'),
        'subtitle' => $activeCode,
        'title_href' => $this->getUrl('about'),
        'languages' => \Defines\Language::getList()
    ));

    if (!$activeCode):
        //  <p>< ? php echo $this->get('content') ? $this->get('content')->getContent() : '...'; ? ></p>
        ?>
        <section class="indent">
            <?php
            foreach (\Defines\Response\Code::getList() as $code):
               ?><p><strong class="co_attention"><?php echo $code ?>:</strong> <?php echo $translate->sys("LB_HEADER_{$code}") ?></p><?php
            endforeach;
            ?>
        </section>
        <?php
    else:
        ?><p class="indent"><strong><?php echo $translate->sys('LB_ERROR') ?></strong>: <?php echo $translate->sys("LB_HEADER_{$activeCode}") ?>...</p><?php
    endif;
    ?>
</article>