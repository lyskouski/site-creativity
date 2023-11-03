<?php
/* @var $this \Engine\Response\Template */
$iNum = $this->get('num', 1);
?>
<header class="el_content_header">
    <?php if ($this->get('button')): ?>
    <aside class="right indent">
        <span class="button bg_attention co_attention ui" data-class="Request/Pjax" data-actions="init" data-data="<?php echo $this->get('button_data') ?>" data-href="<?php echo $this->get('button_href') ?>"><?php echo $this->get('button') ?></span>
    </aside>
    <?php endif ?>

    <?php if ( $this->get( 'title' ) !== null ): ?>
        <h<?php echo $iNum ?> class="title">
            <?php
            if ($this->get('languages') !== null):
                echo $this->partial('Basic/language', array(
                    'languages' => $this->get('languages'),
                    'url' => null
                ));
            endif;
            if ($this->get( 'title_href' ) !== null):
                ?><a href="<?php echo $this->get( 'title_href' ) ?>"><?php
            endif;
            ?><span class="nowrap"><?php echo $this->get( 'title' ); ?></span><?php
            if ($this->get( 'title_href' ) !== null):
                ?></a><?php
            endif;
            ?>
        </h<?php echo $iNum ?>>
    <?php endif ?>
    <?php $iNum++ ?>
    <?php if ( $this->get( 'subtitle' ) !== null ): ?>
        <h<?php echo $iNum ?> class="subtitle">
            <?php
            if ($this->get( 'subtitle_href' ) !== null):
                ?><a href="<?php echo $this->get( 'subtitle_href' ) ?>"><?php
            endif;
            ?><span class="nowrap"><?php echo $this->get( 'subtitle' ); ?></span><?php
            if ($this->get( 'subtitle_href' ) !== null):
                ?></a><?php
            endif;
            if ($this->get( 'sub_languages' ) !== null):
                echo $this->partial( 'Basic/language', array(
                    'languages' => $this->get('sub_languages'),
                    'url' => $this->get('url', null),
                    'url_active' => $this->get('url_active', null)
                ));
            endif; ?>
        </h<?php echo $iNum ?>>
    <?php endif ?>
</header>