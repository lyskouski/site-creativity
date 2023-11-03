<?php
/* @var $this \Engine\Response\Template */
$translate = \System\Registry::translation();

?>
<article class="el_content">
    <?php
    echo $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_SITE_GROUP'),
        'title_href' => $this->getUrl('dev/group'),
        'languages' => \Defines\Language::getList(),
        'subtitle' => $translate->sys('LB_SITE_ACCESS'),
        'subtitle_href' => $this->getUrl('dev/access')
    ));

    $counts = $this->get('counts');

    ?>
    <p class="indent"><?php echo $translate->sys( 'LB_SITE_GROUP_DESCRIPTION' ) ?></p>
    <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl('dev/group', \Defines\Extension::JSON) ?>">
        <div class="el_grid_normalized">
            <div class="el_grid_top">
                <ul class="list">
                    <?php
                    /* @var $access \Data\Doctrine\Main\Access */
                    foreach ($this->get('roles') as $access):
                        ?><li>
                            <a class="button <?php echo $access->getId() === $this->get('active') ? 'bg_button' : 'bg_none' ?> cr_pointer" data-type="submit" data-extra="group=<?php echo $access->getId() ?>&action=view">
                                <span class="el_counter"><?php echo $counts[$access->getId()] ?></span>
                                <?php echo $translate->sys("{$access->getTitle()}") ?>
                            </a>
                        </li><?php
                    endforeach;
                    ?>
                </ul>
            </div>
            <div class="el_grid_top">
                <p class="indent bg_button">
                    <span class="right co_attention"><?php echo $translate->sys("{$this->get('current')->getTitle()}") ?></span>
                    <?php echo $translate->sys('LB_SITE_GROUP') ?>
                </p>
                <p class="indent clear-nowrap"><small><?php echo $translate->sys("{$this->get('current')->getTitle()}_DESCRIPTION") ?></small></p>
                <?php if (!in_array($this->get('current')->getTitle(), $this->get('locked'))): ?>
                <p class="indent">
                    <form method="POST" class="ui" data-class="Request/Form" data-actions="init" action="<?php echo $this->getUrl('dev/group', \Defines\Extension::JSON) ?>">
                        <span class="button bg_button cr_pointer" data-type="submit" data-extra="group=<?php echo $this->get('active') ?>&action=add"><?php echo $translate->sys('LB_BUTTON_ADD') ?></span>
                        <input type="text" name="username" value="" placeholder="<?php echo $translate->sys('LB_AUTH_USERNAME') ?>" />
                    </form>
                </p>
                <?php endif ?>
                <div class="el_scroll">
                    <ul class="indent list">
                        <?php
                        /* @var $userAccess \Data\Doctrine\Main\UserAccess */
                        foreach ($this->get('users') as $userAccess):
                        ?><li>
                            <?php if (!in_array($userAccess->getAccess()->getTitle(), $this->get('locked'))): ?>
                            <span class="button right bg_attention cr_pointer" data-type="submit" data-extra="user_access=<?php echo $userAccess->getId() ?>&group=<?php echo $this->get('active') ?>&action=delete">&Cross;</span>
                            <?php endif ?>
                            <a class="button bg_none indent">
                                <?php echo $userAccess->getUser()->getUsername() ?>
                            </a>
                        </li><?php
                        endforeach;
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </form>
</article>