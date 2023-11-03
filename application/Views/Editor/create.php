<?php /* @var $this \Engine\Response\Template */

$translate = \System\Registry::translation();

$content = array();
/* @var $o \Data\Doctrine\Main\ContentNew */
foreach ($this->get('list') as $o):
    if (strpos($o->getType(),'content#') === 0):
        $content[] = $o->getContent();
    endif;
endforeach;
?>
<script id="ui-editor-data" type="application/ld+json"><?php echo json_encode(['content' => $content]) ?></script>
<form method="POST" class="el_zero_indent indent_top ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl($this->get('url'), \Defines\Extension::JSON) ?>">
    <div class="indent el_border bg_highlight">
        <div class="right">
            <!-- input class="button bg_button" type="button" data-type="submit" value="<?php echo $translate->sys('LB_BUTTON_SAVE') ?>" />
            <input class="button bg_attention" type="button" value="<?php echo $translate->sys('LB_BUTTON_PUBLICATE') ?>" / -->
            <div class="button bg_attention" data-type="submit"><?php echo $translate->sys('LB_BUTTON_SAVE') ?></div>
            <a id="ui-approve-action" class="button bg_note ui" data-class="Modules/Person/Work" data-actions="before" href="<?php echo $this->getUrl($this->get('url') . '/approve/' . $this->get('id')) ?>">
                <?php echo $translate->sys('LB_BUTTON_PUBLICATE') ?>
            </a>
        </div>
        <p><?php echo $this->get('description') ?></p>
    </div>
    <div class="el_footer el_width_auto">
        <span class="ui" data-class="View/Keys" data-actions="init" data-url="">/<?php echo $this->get('url') . '/description/' . $this->get('id') . ':' . $translate->sys('LB_PERSON_DESCRIPTION') ?>,/<?php echo$this->get('url') . '/keywords/' . $this->get('id') . ':' . $translate->sys('LB_FORUM_KEYWORDS') ?>,<?php echo $this->get('keywords') ?></span>
    </div>
    <p class="indent">&nbsp;</p>
</form>
<?php 
foreach ($content as $i => $data):
    ?><article class="el_content el_zero_indent ui" data-class="Ui/Editor" data-actions="init,plainEditor"><?php echo $data ?></article><?php 
endforeach;
if (!$content):
    ?><article class="el_content el_zero_indent ui" data-class="Ui/Editor" data-actions="init,plainEditor"></article><?php 
endif;
?>