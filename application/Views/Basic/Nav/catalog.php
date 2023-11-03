<?php /* @var $this \Engine\Response\Template */

$this->regFunction('cicle', function($aData) {
    if (isset($aData['url'])):
        $sUrl = $aData['url'];
    else:
        $sUrl = $this->getUrl($this->get('url') . '#!/' . $aData['title']);
    endif;
    $sClass = isset($aData['sync']) || $sUrl === '#' ? '' : ' ui';
    $sClassActive = '';
    if ($this->get('url_active') === $aData['title']):
        $sClassActive = 'active';
    endif;
    if ($sClass):
        $sClass .= '" data-class="Request/Pjax" data-actions="init';
    endif;

    if (isset($aData['sub']) && $aData['sub']):
        ?><li><div class="<?php echo $sClassActive ?>">
            <span class="button cr_pointer ui" data-class="View/Height" data-actions="spoiler" data-target="closest:li: > ul" data-status="1" data-invert="&searr;&nbsp;">&nwarr;&nbsp;</span>
            <a href="<?php echo $sUrl ?>" class="<?php echo $sClassActive . $sClass  ?>"><?php echo $aData['title'] ?></a>
        </div>
        <ul class="list"><?php
        foreach ($aData['sub'] as $a):
            $this->evalFunction('cicle', [$a]);
        endforeach;
        ?></ul></li><?php
    else:
        ?><li class="<?php echo $sClassActive ?>"><a href="<?php echo $sUrl ?>" class="<?php echo $sClassActive . $sClass  ?>"><?php echo $aData['title'] ?></a></li><?php
    endif;
});

$bShow = is_string($this->get('url_active'));
$md5hash = md5(json_encode($this->get('list', [])));

if ($this->get('render')):
    foreach ($this->get('list') as $a):
        $this->evalFunction('cicle', [$a]);
    endforeach;
    return;
endif;

?>
<aside class="right el_catalog" id="uid<?php echo $md5hash ?>">
    <aside class="el_catalog_nav cr_pointer ui" data-class="View/Height" data-actions="spoiler" data-target="closest:.el_catalog: .el_grid_top" data-status="<?php echo (int)$bShow ?>">
        <div class="<?php echo $this->get('align', 'right') ?> button bg_button"> &equiv;</div>
    </aside>
    <div class="el_grid">
        <div class="el_grid_top<?php echo $bShow ? '' : ' el_hidden' ?>">
            <ul class="list">
                <?php
                foreach ($this->get('list') as $a):
                    if (is_string($a)):
                        echo $a;
                    else:
                        $this->evalFunction('cicle', [$a]);
                    endif;
                endforeach;
                ?>
            </ul>
        </div>
    </div>
    <div>&nbsp;</div>
</aside>