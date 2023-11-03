<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

if (!$this->get('search') && \System\Registry::user()->checkAccess($this->get('module_url'), 'create')):
    echo $this->partial('forum/new');
endif;

if ($this->get('list')):
    echo $this->partial('Basic/table');
else:
    $test = $translate->sys('LB_CONTENT_IS_MISSING');
    ?><p class="indent"><?php echo substr($test, 0, strpos($test, '.')) ?> (<a href="<?php echo $aHeader['subtitle_href'] ?>"><?php echo $translate->sys('LB_FORUM_CREATE_TOPIC') ?></a>).</p><?php
endif;

/* @var $oStat \Data\Doctrine\Main\ContentViews */
$oStat = $this->get('stat');
if ($oStat && $oStat->getContentCount() > \Defines\Database\Params::COMMENTS_ON_PAGE):
    echo $this->partial('Basic/Nav/pages', array(
        'curr' => $this->get('page'),
        'num' => \Defines\Database\Params::getPageCount($oStat),
        'url' => $this->get('url')
    ));

elseif ($this->get('count')):
    echo $this->partial('Basic/Nav/pages', array(
        'curr' => $this->get('page'),
        'num' => \Defines\Database\Params::getPageCount($this->get('count'), $this->get('count_page')),
        'url' => $this->get('count_url')
    ));
endif;

?>
</article>