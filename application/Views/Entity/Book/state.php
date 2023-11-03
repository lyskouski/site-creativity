<?php /* @var $this \Engine\Response\Template */

$translate = \System\Registry::translation();

/* @var $bookTitle \Data\Doctrine\Main\Content */
$bookTitle = $this->get('entity');

if (!$this->get('origin')):
    ?><br /><?php
endif;
// Already in a list
if ($this->get('read')):
    /* @var $readList \Data\Doctrine\Main\BookRead */
    foreach ($this->get('read') as $readList):
        ?><div data-isbn="<?php echo $readList->getBook()->getId() ?>">
            <div class="el_border bg_form indent_vertical co_link">
                &nbsp;<?php echo \Defines\Database\BookCategory::getIcon($readList->getStatus()) ?>&nbsp;
                <a class="co_link" href="<?php echo $this->getUrl($readList->getContent()->getPattern()) ?>"><?php echo $readList->getContent()->getContent() ?></a>
            </div>
        </div>
        <?php
    endforeach;

// Available list for reading  style="margin:-12px 0 -14px"
elseif ($this->get('list')):
    ?>
    <form <?php
        if ($this->get('origin')):
            ?> class="ui indent_ext indent_neg_bottom"<?php
        else:
            ?> class="ui" id="list_name"<?php
        endif; ?> method="POST" data-class="Request/Form" data-actions="init" action="">
        <input type="hidden" name="action" value="move" />
        <input type="hidden" name="pos" value="0" />
        <input type="hidden" name="type" value="<?php echo \Defines\Database\BookCategory::WISHLIST ?>" />
        <input type="hidden" name="isbn" value="<?php echo $translate->get(['isbn', $bookTitle->getPattern()]) ?>" />
        <select <?php
            if ($this->get('origin')):
                ?>class="ui el_select" data-class="Request/Form" data-actions="change"<?php
            else:
                ?>class="ui" data-class="Ui/Element" data-actions="select"<?php
            endif; ?> data-callback="Modules/Book/Calendar:push">
            <option disabled selected="selected"><?php echo $translate->sys('LB_BOOK_LIST_ADD') ?>...</option>
            <?php
            /* @var $o \Data\Doctrine\Main\Content */
            foreach ($this->get('list') as $o):
                ?><option value="<?php echo $this->getUrl($o->getPattern(), \Defines\Extension::JSON) ?>"><?php echo $o->getContent() ?></option><?php
            endforeach;
            ?>
        </select>
    </form>
    <?php
// Extra buttons
elseif ($this->get('book_aside', null) !== null):
    ?><div class="indent_vertical"><?php
    foreach ($this->get('book_aside') as $btn):
        ?><a class="button <?php echo $btn['ui'] ?>"><?php echo $btn['title'] ?></a><?php
    endforeach;
    ?></div><?php
// Missing lists
else:
    ?><a class="co_attention" href="<?php echo $this->getUrl('book/calendar') ?>"><?php echo $translate->sys('LB_BOOK_LIST_MISSING') ?></a><?php
endif;