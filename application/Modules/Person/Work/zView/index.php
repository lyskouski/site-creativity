<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();
$imgPath = new \System\Minify\Images();
?>
<article class="el_content ui" data-class="Modules/Person" data-actions="init" data-type="person">
    <?php
    echo $this->partial(
        'Basic/title',
        array(
            'title' => $translate->sys('LB_PERSONAL'),
            'subtitle' => $translate->sys('LB_PERSON_WORK_NEW'),
            'languages' => \Defines\Language::getList()
        )
    );

    $exclude = array();
    foreach (\Defines\Content\Type::getList() as $sType):
        $data = array(
            'img' => "{$imgPath->getWork()}{$sType}.svg",
            'img_type' => "{$imgPath->getWork()}{$sType}_type.svg",
            'updated_at' => '',
            'title' => $translate->sys('LB_OEUVRE_' . strtoupper($sType)),
            'text' => $translate->sys('LB_OEUVRE_' . strtoupper($sType) . '_DESC'),
            'href' => $this->getUrl('person/work/' . $sType)
        );

        if (!\System\Registry::user()->checkAccess('person/work/' . $sType)):
            $exclude[] = $data;
        else:
            echo $this->partial('Basic/notion', $data);
        endif;
    endforeach;

    foreach ($exclude as $data):
        ?>
        <section class="el_table indent">
            <header>
                <img class="left indent" width="24px" heigth="24px" src="<?php echo $data['img_type'] ?>" />
                <a href="<?php echo $data['href'] ?>">
                    <img width="12px" height="12px" alt="<?php echo $translate->sys('LB_HEADER_423') ?>" src="<?php echo $imgPath->get() ?>icon/locked.svg" />
                    <?php echo $data['title'] ?>
                </a>
                <p><?php echo $translate->sys('LB_ERROR_PRIVILEGES_LIMITATION') ?>
                <a class="inline" href="<?php echo $this->getUrl('dev/proposition/i20165') ?>"><?php echo $translate->get(['og:title', 'dev/proposition/i20165']) ?></a>
                </p>
            </header>
        </section>
        <?php
    endforeach;

    if ($exclude):
        ?><p class="indent">&nbsp;</p><?php
    endif;

    echo $this->partial(
        'Basic/title',
        array(
            'num' => 2,
            'title' => $translate->sys('LB_PERSON_DRAFT')
        )
    );

    if (!$this->get('draft')):
        ?><p class="indent clear"><?php echo $translate->sys('LB_ERROR_MISSING_DATA') ?></p><?php
    endif;

    /* @var $o \Data\Doctrine\Main\ContentNew */
    foreach ($this->get('draft') as $o):
        ?>
        <section class="el_table indent">
            <header>
                <?php
                switch ($o->getAccess()):
                    case \Defines\User\Access::getAudit():
                        echo '<small class="right co_attention">', $translate->sys('LB_PERSON_SUBMIT_PUBLIC'), '</small>';
                        break;
                endswitch;
                ?>
                <a href="<?php echo $this->getUrl($o->getPattern(), null, $o->getLanguage()) ?>">
                    <?php if (strpos($o->getPattern(), 'person') === 0): ?>
                    <img width="12px" height="12px" src="<?php echo $imgPath->adaptWorkUrl($o->getPattern(), '_type') ?>" />
                    <?php endif ?>
                    <?php echo $o->getContent() ?>
                </a>
                <small>
                    <?php echo $translate->sys('LB_SITE_UPDATES') ?>:
                    <?php echo $o->getUpdatedAt()->format(\Defines\Database\Params::TIMESTAMP) ?>
                </small>
            </header>
        </section>
        <?php
    endforeach;

    ?><p class="indent">&nbsp;</p><?php

    echo $this->partial(
        'Basic/title',
        array(
            'num' => 2,
            'title' => $translate->sys('LB_PERSON_WORK_PUBLISHED'),
            'languages' => \Defines\Language::getList()
        )
    );

    if (!$this->get('artwork')):
        ?><p class="indent clear"><?php echo $translate->sys('LB_ERROR_MISSING_DATA') ?></p><?php
    else:
        echo $this->partial('Entity/Basic/list', array(
            'list' => $this->get('artwork')
        ));
    endif;

    if (sizeof($this->get('artwork')) === \Modules\Person\Work\Model::ARTWORK_LIST):
        ?><section class="indent"><a href="<?php echo $this->getUrl('person/' . \System\Registry::user()->getName() . '/artwork') ?>" class="button bg_attention"><?php echo $translate->sys( 'LB_GET_DETAILS' ) ?></a></section><?php
    endif;

    // @todo artwork pages

    ?>
</article>