<?php
/* @var $this \Engine\Response\Template */
use Defines\Database\BoardCategory;

$translate = \System\Registry::translation();

?>
<article class="el_content">
    <p>&nbsp;</p>
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_SITE_BOARD_LIST'),
        'title_href' => $this->getUrl('dev/board'),
        'languages' => \Defines\Language::getList()
    ));
    ?>
    <section class="el_table_pair clear">
        <?php
        $taskUrl = null;
        foreach ($this->get('subtask') as $o):
            if ($o->getPattern() !== $taskUrl):
                $taskUrl = $o->getPattern();
                echo $this->partial('Basic/notion_plain', array(
                    'href' => $taskUrl,
                    'updated_at' => $o->getUpdatedAt()->format(\Defines\Database\Params::DATE_FORMAT),
                    'title' => $translate->get(['og:title', $taskUrl]),
                    'async' => false,
                    'draggable' => false,
                    'author' => \Data\UserHelper::getUsername($o->getAuthor()),
                    'text' => $translate->get(['description', $taskUrl])
                ));
            endif;
            ?>
            <div class="el_table indent">
                <div class="right ui-select-width" data-width="200">
                    <select name="subtask[<?php echo $o->getId() ?>]" class="ui" data-class="Ui/Element" data-actions="select" data-callback="Modules/Dev/Board:state">
                        <?php foreach (BoardCategory::getList() as $status): ?>
                        <option value="<?php echo $status ?>" <?php echo $o->getAccess() === $status ? 'selected' : '' ?>><?php echo BoardCategory::getName($status) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <?php echo BoardCategory::getIcon($o->getAccess(), $translate->get([$o->getType(), $o->getPattern()])) ?>
            </div>
            <?php
        endforeach;
        ?>
        <p>&nbsp;</p>
    </section>
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_SITE_BOARD_TASK'),
        'num' => 2
    ));
    ?>
    <section>
        <?php
        $content = $this->get('new');
        /* @var $content array<\Data\Doctrine\Main\Content> */
        foreach ($content[\Defines\Content\Attribute::TYPE_TITLE] as $s => $title):
            echo $this->partial('Basic/notion', array(
                'author' => \Data\UserHelper::getUsername($title->getAuthor()),
                'title' => $title->getContent(),
                'href' => $this->getUrl($s),
                'async' => false,
                'callback' => 'cr_move ui" data-class="View/DragDrop" data-actions="drag" data-pattern="' . $s,
                'draggable' => true,
                'img_type' => $content['image'][$s],
                'img' => $content[\Defines\Content\Attribute::TYPE_IMG][$s],
                'text' => $content[\Defines\Content\Attribute::TYPE_DESC][$s],
                'updated_at' => $title->getUpdatedAt()->format(\Defines\Database\Params::DATE_FORMAT)
            ));
        endforeach;
        ?>
    </section>
    <section class="el_grid">
        <div class="el_grid" id="read_list">
            <?php
            echo $this->partial('list', array(
                'list' => $this->get('list_new', []),
                'title' => $translate->sys('LB_DEV_BOARD_NEW'),
                'type' => BoardCategory::RECENT,
                'header' => 'bg_form'
            ));

            echo $this->partial('list', array(
                'list' => $this->get('list_active', []),
                'title' => $translate->sys('LB_DEV_BOARD_ACTIVE'),
                'type' => BoardCategory::ACTIVE,
                'header' => 'bg_accepted',
                'class' => 'el_border el_table_newline'
            ));

            echo $this->partial('list', array(
                'list' => $this->get('list_finish', []),
                'title' => $translate->sys('LB_DEV_BOARD_FINISH'),
                'type' => BoardCategory::FINISH,
                'header' => 'bg_note',
                'class' => 'el_table_newline'
            ));
            ?>
        </div>
    </section>
</article>