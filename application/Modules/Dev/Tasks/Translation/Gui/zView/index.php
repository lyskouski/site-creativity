<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$sActive = $this->get('url_active');

echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content">
    <p>&nbsp;</p>
    <?php echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_TASK_TRANSLATION_INTERFACE'),
        'title_href' => '',
        'languages' => \Defines\Language::getList(),
        'subtitle' => $translate->sys('LB_TARGET'),
        'sub_languages' => array_diff(\Defines\Language::getList(), array($translate->getTargetLanguage())),
        'url' => '/{focus}/{module}#!/{language}.' . \Defines\Extension::getDefault(),
        'url_active' => $sActive
    )); ?>
    <section class="indent clear">
        <p><input type="checkbox" onchange="jQuery('.ui-element1')[this.checked ? 'hide' : 'show']()" /> <?php echo $translate->sys('LB_TASKS_TRANSLATION_MISSING') ?></p>
        <p>&nbsp;</p>
        <form method="POST" action="<?php echo $this->getUrl('dev/tasks/translation/gui/' . $sActive, \Defines\Extension::JSON, $translate->getTargetLanguage()) ?>" class="ui" data-class="Request/Form" data-actions="init">
            <?php foreach ($this->get('list', array()) as $sName): ?>
            <div class="ui-element<?php echo strpos($translate->sys($sName, $sActive), '{{') !== false ? 0 : 1 ?>">
                <div class="el_form el_full bs_button">
                        <span class="right im_left"><span class="im_lang im_lang_<?php echo $sActive ?>">&nbsp;</span></span>
                        <span class="im_left"><span class="im_lang im_lang_<?php echo $translate->getTargetLanguage() ?>">&nbsp;</span></span>
                        &nbsp;<?php echo $sName ?>
                        <a href="#" class="fs_small indent co_attention ui"
                           data-class="Request/Translation" data-actions="yandex" data-from="<?php echo $translate->getTargetLanguage() ?>" data-to="<?php echo $sActive ?>"
                           data-content="<?php echo $translate->sys($sName) ?>">
                               <?php echo $translate->sys('LB_TRANSLATION_HELP') ?>
                        </a>
                </div>
                <div class="el_full el_grid_normalized">
                    <div class="el_normal el_grid_top">
                        <p class="indent"><?php echo $translate->sys($sName) ?></p>
                    </div>
                    <div class="el_normal el_grid_top">
                        <p class="indent"><textarea class="ui" data-class="Request/Translation" data-actions="init" data-name="list[<?php echo $sName ?>]" lang="<?php echo $sActive ?>"><?php echo $translate->sys($sName, $sActive) ?></textarea></p>
                    </div>
                </div>
            </div>
            <?php endforeach ?>
            <input type="hidden" name="language" value="<?php echo $sActive ?>" />
            <input type="hidden" name="action" value="save" />
            <input class="bg_attention" type="submit" value="<?php echo $translate->sys('LB_BUTTON_SAVE') ?>" />
        </form>
    </section>
</article>