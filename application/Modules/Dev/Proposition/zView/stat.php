<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

?>
<p class="el_border indent bg_mask clear"><?php echo $translate->sys('LB_DEV_PROPOSITION_WORKFLOW') ?>:</p>
<?php
foreach ($this->get('stat') as $title => $stat):
    reset($stat);
    $url = current($stat);
    ?>
    <div class="el_abs"><a href="<?php echo $this->getUrl($url) ?>"><?php echo $translate->get(['og:title', $url]) ?></a></div>
    <div class="el_grid">
        <?php
        for ($i = 30; $i >= 0; $i--):
            $date = date('Y-m-d', strtotime("-{$i} days"));
            if (array_key_exists($i, $stat)):
                ?><span class="el_border bg_accepted" title="<?php echo $date ?>">&nbsp;</span><?php
            else:
                ?><span class="el_border">&nbsp;</span><?php
        endif;
    endfor;
    ?></div><?php
endforeach;
?>
<div>
    <span class="right">&dashv;</span>
    <sup class="right co_approve"><?php echo (new \System\Converter\DateDiff)->getInterval((new \DateTime)->diff(new \DateTime)) ?></sup>
    <span class="left">&vdash;</span>
    <sup class="left co_approve"><?php echo (new \System\Converter\DateDiff)->getInterval((new \DateTime('-1 month'))->diff(new \DateTime)) ?></sup>
</div>