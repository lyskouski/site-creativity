<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

?>
<section>
    <search id="search_option">
        <div class=" ui" data-class="Modules/Book/Calendar" data-actions="autoSearch">
            <p class="indent"><?php
            foreach ($this->get('list') as $i => $data):
                ?><a href="#" class="el_border indent ui-search-option ui" data-class="Modules/Book/Calendar" data-actions="search"><?php 
                    echo $data['name'];
                ?></a><?php
            endforeach;
            ?></p>
        </div>
    </search>
</section>