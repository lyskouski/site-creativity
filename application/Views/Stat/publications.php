<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$oModel = new \Modules\Person\Stat\Model();

$aParams = explode(',', $this->get('data', 'date,DESC'));

$aData = array();
$aBold = new \System\ArrayUndef();
switch ($aParams[0]):
    case 'rating':
        $sOrderName = $translate->sys('LB_ORDER_RATING');
        break;
    case 'views':
        $sOrderName = $translate->sys('LB_ORDER_VIEWS');
        break;
    default:
        $sOrderName = $translate->sys('LB_ORDER_DATE');
        $aBold['date'] = 'strong';
        $aData = $oModel->getPublications($this->get('user'), array('updatedAt' => $aParams[1]));
endswitch;

if ($this->get('edit') && !$this->get('data')):
    ?><p><?php echo $translate->sys('LB_PERSON_MODULE_SECTION') ?>:</p><?php
endif;

$sClass = $this->get('ui', 'ui-delay');
?>

<section class="el_panel el_border <?php
    if ($this->get('edit')):
        echo $sClass; ?> el_border_dashed" data-type="<?php echo $this->get('type', 'publications') ?>" data-value="<?php echo $this->get('data', 'date,DESC') ?>" data-class="View/DragDrop" data-actions="drag<?php
    endif;
    ?>">
    <header class="bg_normal">
        <span class="bg_normal right">&dtrif;&nbsp;<?php echo $sOrderName ?></span>
        <?php echo $translate->sys('LB_PERSON_PUBLICATIONS') ?>
    </header>
    <?php
    if ($aData):
        ?><ul><?php
        /* @var $o \Data\Doctrine\Main\Content */
        foreach ($aData as $o):
            ?><li class="nowrap">
                <strong class="right">
                    <?php switch ($aParams[0]):
                        case 'rating':
                            echo 1 . '&nbsp;/&nbsp;' . \Defines\Database\Params::MAX_RATING ; // @todo
                            break;
                        case 'views':
                            echo 1;// @todo
                            break;
                        default:
                            echo $o->getUpdatedAt()->format('Y-m-d');
                    endswitch; ?>
                </strong>
                <a href="<?php echo $this->getUrl($o->getPattern(), \Defines\Extension::getDefault(), $o->getLanguage())?>" target="_blank">
                    <?php echo $o->getContent() ?>
                </a>
            </li><?php
        endforeach;
        ?></ul><?php
    else: ?>
    <p><?php echo $translate->sys('LB_HEADER_404') ?></p>
    <?php endif ?>
</section>