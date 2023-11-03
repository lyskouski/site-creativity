<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

// echo $this->partial('Basic/Nav/crumbs');
?>
<article class="el_content">
    <form method="POST" class="clear_mrg ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl($this->get('pattern'), \Defines\Extension::JSON) ?>">
        <section class="el_form indent">
            <aside class="right">
                <div class="button bg_attention" data-type="submit"><?php echo $translate->sys('LB_BUTTON_MODIFY') ?></div>
                <a id="ui-approve-action" class="button bg_note ui" data-class="Modules/Person/Work" data-actions="before" href="<?php echo $this->getUrl($this->get('url') . '/approve/' . $this->get('id')) ?>">
                    <?php echo $translate->sys('LB_BUTTON_PUBLICATE') ?>
                </a>
            </aside>
            <?php echo $this->get('description') ?>
        </section>
        <input type="hidden" name="action" value="change" />
        <?php
        $list = array();
        /* @var $o \Data\Doctrine\Main\ContentNew */
        foreach ($this->get('list') as $o):
            if ($o->getType() === 'content#0'):
                $list = explode(',', $o->getContent());
            endif;
            ?><input type="hidden" name="<?php echo $o->getType() ?>" value="<?php echo $o->getContent() ?>" /><?php
        endforeach;
        ?>
    </form>

    <section class="indent">
        <?php echo $this->partial('Entity/Book/search', array(
            'href' => 'book/calendar'
        )); ?>

        <p class="indent clear">&nbsp;</p>
        <div class="el_grid_top ui" data-class="View/DragDrop" data-actions="drop" data-callback="Modules/Person/Work/Book/Series:add" data-pos="<?php echo sizeof($list) + 1 ?>">
            <header class="indent el_border ui" data-class="View/DragDrop" data-actions="drop" data-callback="Modules/Person/Work/Book/Series:add" data-pos="0">
                <h3><?php echo $translate->sys('LB_OEUVRE_BOOK_SERIES') ?></h3>
            </header>

            <div class="el_table_pair indent_vertical">
                <?php
                echo $this->partial('Entity/Book/Series/list', array(
                    'list' => $list,
                    'callback' => 'ui" data-class="View/DragDrop" data-actions="drag,drop" data-callback="Modules/Person/Work/Book/Series:add',
                    'buttons' => array(array(
                        'ui' => 'bg_attention ui" data-class="Modules/Person/Work/Book/Series" data-actions="remove',
                        'title' => $translate->sys('LB_BUTTON_DELETE')
                    ))
                ));

                for ($i = sizeof($list); $i < 6; $i++):
                    ?>
                    <div class="el_table indent ui" data-class="View/DragDrop" data-actions="drop" data-callback="Modules/Person/Work/Book/Series:add" data-pos="<?php echo $i ?>">
                        <?php echo $i ?>. ...
                    </div>
                    <?php
                endfor;
                ?>
            </div>
        </div>
    </section>
    <p class="indent">&nbsp;</p>
</article>