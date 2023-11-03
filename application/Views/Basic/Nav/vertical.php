<?php /* @var $this \Engine\Response\Template */ ?>
<div class="el_grid_top el_vertical">
    <div class="ui menu" data-class="Ui/Element" data-actions="menu" data-rotate="<?php echo $this->get('rotate', 'lf') ?>">
        <?php foreach ($this->get('menu', array()) as $sUrl => $sTitle): ?>
        <a class="ui button <?php echo $this->get('color', 'bg_normal') ?> <?php echo $this->get('active') === $sUrl ? ' active' : '' ?>" href="<?php echo $this->getUrl( $sUrl ) ?>" data-data="{}" data-class="Request/Pjax" data-actions="init">
            <?php echo $sTitle ?>
        </a>
        <?php endforeach ?>
    </div>
</div>