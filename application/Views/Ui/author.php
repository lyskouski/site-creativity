<?php /* @var $this \Engine\Response\Template */

if ($this->get('author')):
    ?><a href="<?php echo $this->getUrl('person/' . $this->get('author')) ?>"><?php
        echo $this->get('author');
    ?></a><?php
else:
    ?><blockquote><?php echo $this->get('error') ?></blockquote><?php
endif;