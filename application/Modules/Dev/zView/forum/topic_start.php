<?php
/* @var $this \Engine\Response\Template */

echo $this->partial('Basic/Nav/crumbs');

/* @var $oStat \Data\Doctrine\Main\ContentViews */
$oStat = $this->get('stat');
$translate = \System\Registry::translation();

if (!$oStat || !$oStat->getContent()):
    throw new \Error\Validation($translate->sys('LB_ERROR_INCORRECT_REQUEST'), \Defines\Response\Code::E_NOT_FOUND);
endif;

$sAccessTopic = $oStat->getContent()->getAccess();
$oValid = new \Access\Validate\Check();
if (!$oValid->setType(\Defines\User\Access::READ)->isAccepted() && !\System\Registry::user()->checkAccess('dev/tasks/moder/topics')):
    throw new \Error\Validation($translate->sys('LB_HEADER_423'));
endif;

$pattern = $oStat->getContent()->getPattern();

$aLanguages = (new \Data\ContentHelper)->getRepository()->getContentLanguages($pattern);

?>
<article class="el_content el_table_pair">
    <p>&nbsp;</p>
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $translate->get(['og:title', $pattern]),
        'title_href' => $this->getUrl($this->get('title_href')),
        'languages' => sizeof($aLanguages) > 1 ? $aLanguages : null,
        'subtitle' => $translate->get(['og:title', $this->get('module_url')]),
        'subtitle_href' => $this->getUrl($this->get('module_url'))
    ));

    if (\System\Registry::user()->checkAccess($this->get('module_url'), 'edit')):
        ?><aside class="right indent el_border">
            <div class="indent">
                <?php echo $translate->sys('LB_ACCESS') ?>: <?php echo $this->partial('Basic/Desc/access', array('value' => $oStat->getContent()->getAccess())) ?>
                <span class="button indent bg_button ui" data-class="Request/Pjax" data-actions="init" data-href="<?php echo $this->getUrl($this->get('title_href'), \Defines\Extension::JSON) ?>" data-data="{'action':'edit'}">
                    <?php echo $translate->sys('LB_BUTTON_EDIT') ?>
                </span>
            </div>
        </aside><?php
    endif;

    echo $translate->get(['task', $this->get('title_href')], null, function($data) {
        if ($data[0] !== '{'):
            return '<aside class="right indent el_border bg_highlight">' . \Defines\Database\BoardCategory::getName($data) . '</aside>';
        endif;
    });

    ?>
    <section class="indent">
        <?php
        $minLang = min(array_keys($aLanguages));
        if ($aLanguages[$minLang] !== $translate->getTargetLanguage()):
            ?><a title="<?php echo $translate->get(['og:title', $pattern], $aLanguages[$minLang]) ?>" class="co_attention" href="<?php echo $this->getUrl($pattern, null, $aLanguages[$minLang]) ?>"><?php echo $translate->sys('LB_ORIGINAL_PAGE') ?></a><?php
        else:
            $username = \Data\UserHelper::getUsername($oStat->getContent()->getAuthor());
            ?><a href="<?php echo $this->getUrl('person/' . $username) ?>"><?php echo $username ?></a><?php
        endif;
        ?>:
        <?php echo $translate->get(['description', $pattern], $translate->getTargetLanguage()); ?>
        <footer class="el_footer">
            <div class="right"><?php echo $this->partial('Entity/share', array('class' => 'right')) ?></div>
            <div class="left ui" data-class="View/Keys" data-actions="init" data-url="<?php echo $this->getUrl($this->get('module_url'), '') ?>"><?php echo $translate->get(['keywords', $pattern], $translate->getTargetLanguage()); ?></div>
        </footer>
    </section>

    <?php
    $access = $oStat->getContent()->getAccess();
    if (
            $access[0] != \Defines\User\Access::BLOCK
            && sizeof(\Defines\Language::getList()) > sizeof($aLanguages)
            && \System\Registry::user()->checkAccess($this->get('module_url'), 'translate')
            && (
                $access[0] >= \Defines\User\Access::TRANSLATE && $oStat->getContent()->getAuthor() === \System\Registry::user()->getEntity()
                || $access[1] >= \Defines\User\Access::TRANSLATE && $access[1] < \Defines\User\Access::BLOCK
            )
    ):
        ?>
        <section class="el_form ">
            <div class="indent">
                <?php
                echo $translate->sys('LB_ACCESS_TASKS_TRANSLATOR'), ': ';
                foreach (\Defines\Language::getList() as $sLanguage):
                    if (in_array($sLanguage, $aLanguages)):
                        continue;
                    endif;
                    $s = strtoupper($sLanguage);
                    ?><div class="button bg_button ui" title="<?php echo $translate->sys('LB_TRANSLATION_HELP'), ': <b>', $translate->sys("LB_LANG_{$s}"), '</b>'; ?>" data-class="Request/Pjax" data-actions="init" data-href="<?php echo $this->getUrl($this->get('title_href'), '') ?>" data-data="{'action':'translate', 'language':'<?php echo $sLanguage ?>'}"><?php echo $sLanguage ?></div> <?php
                endforeach;
                ?>
            </div>
        </section>
        <?php
    endif;