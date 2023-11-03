<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

$pages = 0;

/* @var $o \Data\Doctrine\Main\BookRead */
foreach ($this->get('list') as $o):
    if (in_array($o->getStatus(), [\Defines\Database\BookCategory::FINISH, \Defines\Database\BookCategory::DELETE])):
        continue;
    endif;
    $pages += $o->getBook()->getPages() - $o->getPage();
endforeach;

if ($this->get('search')): ?>
<section>
    <search id="read_stat">
    <?php endif ?>
        <small class="indent"><?php echo sprintf(
            $translate->sys('LB_BOOK_LIST_DAYS_LEFT'),
            (string) $pages,
            $this->get('pages/day') ? \Defines\Database\Params::getNumber($pages / $this->get('pages/day')) : "{{$translate->sys('LB_HEADER_203')}}"
        ) ?></small>
        <p class="indent">&nbsp;</p><?php
    if ($this->get('search')): ?>
    </search>
</section>
<?php
endif;