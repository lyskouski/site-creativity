<?php /* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

if ($this->get('entity')):
    $text = $this->get('entity');
    $type = $text->getType();
    $url = $text->getPattern();
else:
    $type = $this->get('type', 'content#0');
    $url = $this->get('url');
    $text = $translate->entity([$type, $url]);
endif;

if ($this->get('updated')):
    ?>
    <aside class="indent">
        <div class="right">
            <?php echo $translate->sys('LB_SITE_UPDATES') ?>:
            <span class="co_attention"><?php echo $text->getUpdatedAt()->format(Defines\Database\Params::TIMESTAMP) ?></span>
            (<a href="<?php echo $this->getUrl('dev/history/' . $text->getId() ) ?>"><?php echo $translate->sys('LB_FORUM_HISTORY') ?></a>)
        </div>
    </aside>
    <?php
endif;

if (\System\Registry::user()->checkAccess('dev/tasks/auditor', 'update')):
    ?><section class="indent ui clear" data-class="Modules/Person/Work" data-actions="update" data-id="<?php echo $text->getId() ?>" itemprop="articleBody mainEntityOfPage"><?php
else:
    ?><section class="indent clear" itemprop="articleBody mainEntityOfPage"><?php
endif;

echo $translate->get([$type, $url], null, function($data) {
    if (!$data || $data[0] === '{') {
        $data = '<p>' . \System\Registry::translation()->sys('LB_CONTENT_IS_MISSING') . '</p>';
    }
    return $data;
});

?></section>