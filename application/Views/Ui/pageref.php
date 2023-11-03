<?php /* @var $this \Engine\Response\Template */

if ($this->get('text')):
    ?><a href="<?php echo $this->getUrl($this->get('url'), null, $this->get('lang')) ?>" title="<?php
        echo $this->get('text'), ' (', $this->get('author'), ')';
    ?>"><?php
        echo $this->get('text');
    ?></a><?php
else:
    ?><blockquote><?php echo $this->get('error') ?></blockquote><?php
endif;