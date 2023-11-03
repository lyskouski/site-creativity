<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

$iPage = $this->get('curr') + 1;
$iNum = $this->get('num') + 1;
$sUrl = $this->get('url', '');

if ($this->get('num') > 0):
    ?><header class="el_content_header el_grid">
        <?php
        if ($iPage === 2):
            $this->link()->meta(new \Engine\Response\Meta\Link('prev', $this->getUrl($sUrl)), true);
            ?><a href="<?php echo $this->getUrl($sUrl) ?>" class="title ui" data-class="Request/Pjax" data-actions="init"><?php echo $translate->sys('LB_PAGE_PREVIOUS') ?></a><?php

        elseif ($iPage >= 2):
            $this->link()->meta(new \Engine\Response\Meta\Link('prev', $this->getUrl($sUrl . '/' . ($iPage - 2))), true);
            ?><a href="<?php echo $this->getUrl($sUrl . '/' . ($iPage - 2)) ?>" data-href="<?php echo $this->getUrl($sUrl . '#!/' . ($iPage - 2)) ?>" class="title ui" data-class="Request/Pjax" data-actions="init"><?php echo $translate->sys('LB_PAGE_PREVIOUS') ?></a><?php

        else:
            ?><span class="title inactive cr_default"><?php echo $translate->sys('LB_PAGE_PREVIOUS') ?></span><?php
        endif;
        ?>
        <a class="el_trapeze center">
            <div>
                <?php echo $translate->sys('LB_PAGE') ?>
                <input max="<?php echo $this->get('num') ?>" min="0" type="text" size="3" value="<?php echo $iPage ?>" class="center ui" data-class="Request/Pjax" data-actions="page" data-url="<?php echo $this->getUrl($sUrl, false) ?>" />
                <?php echo $translate->sys('LB_PAGE_COUNT'), ' ', $iNum ?>
            </div>
        </a>
        <?php
        if ($iPage < $iNum):
            $this->link()->meta(new \Engine\Response\Meta\Link('next', $this->getUrl($sUrl . '/' . $iPage)), true);
            ?><a  href="<?php echo $this->getUrl($sUrl . '/' . $iPage) ?>" data-href="<?php echo $this->getUrl($sUrl . '#!/' . $iPage) ?>" class="subtitle ui" data-class="Request/Pjax" data-actions="init"><?php echo $translate->sys('LB_PAGE_NEXT') ?></a><?php

        else:
            ?><span class="subtitle inactive cr_default"><?php echo $translate->sys('LB_PAGE_NEXT') ?></span><?php
        endif;
        ?>
    </header><?php
endif;