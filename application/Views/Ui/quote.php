<?php /* @var $this \Engine\Response\Template */ ?>
<blockquote>
    <?php
    if ($this->get('url')):
        ?><a href="<?php echo $this->getUrl($this->get('url'), null, $this->get('lang')) ?>">&DDotrahd;</a>&nbsp;<?php
    endif;
    echo $this->get('text');
    if ($this->get('author')):
        ?><q><a href="<?php echo $this->getUrl('person/' . $this->get('author')) ?>"><?php
            echo $this->get('author');
        ?></a></q><?php
    endif;
    ?>
</blockquote>