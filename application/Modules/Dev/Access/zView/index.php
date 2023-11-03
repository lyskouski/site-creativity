<?php /* @var $this \Engine\Response\Template */ ?>
<article class="el_content">
    <?php
    $translate = \System\Registry::translation();

    echo
    $this->partial('Basic/title', array(
        'title' => $translate->sys('LB_SITE_ACCESS'),
        'title_href' => $this->getUrl('dev/access'),
        'languages' => \Defines\Language::getList(),
        'subtitle' => $translate->sys('LB_SITE_GROUP'),
        'subtitle_href' => $this->getUrl('dev/group'),
    ));

    ?><section class="indent ui"  data-class="Modules/Dev/Access" data-actions="init">
        <p><?php echo $translate->sys( 'LB_ACCESS_TEXT_DESCRIPTION' ) ?></p>
        <p>&nbsp;</p>
        <div class="el_grid">
            <div class="el_grid_top">
            <?php
            $aData = $this->get('list');

            // add new button
            $fNew = function() use ($translate) {
                ?><ul class="list ui" data-class="View/DragDrop" data-actions="drop">
                    <li><div class="indent cr_default"><span class="db-add button cr_pointer">&erarr;</span> <?php echo $translate->sys( 'LB_ACCESS_ADD_NEW' ) ?></div></li>
                </ul><?php
            };
            $this->regFunction('new', $fNew);

            // temporary function for a recursion functionality
            $fCircle = function($id, $aAccess) use ($translate, $aData) {
                if (in_array($id, array(\Access\User::ADMIN, \Access\User::BLOCKED))):
                    ?><ul class="list">
                        <li class="db-access" data-id="<?php echo $id ?>" data-title="<?php echo $aAccess['title'] ?>">
                            <a class="button bg_button cr_default ui" href="#" data-class="View/Href" data-actions="preventDefault">
                                <b><?php echo $aAccess['title'] ?></b>
                            </a>
                        </li>
                    </ul><?php
                    return;
                endif;
                ?><ul class="list ui" data-class="View/DragDrop" data-actions="drop">
                    <li class="db-access ui" data-class="View/DragDrop" data-actions="drag" data-id="<?php echo $id ?>" data-title="<?php echo $aAccess['title'] ?>">
                        <a class="button bg_button cr_default ui" href="#" data-class="View/Href" data-actions="preventDefault">
                            <span class="button right bg_attention cr_pointer ui" data-class="View/Height" data-actions="remove" data-target="closest:li:">&Cross;</span>
                            <?php
                            if ($aAccess['sub']):
                                ?><span class="button cr_pointer ui" data-class="View/Height" data-actions="spoiler" data-target="closest:li: > ul" data-status="1" data-invert="&searr;&nbsp;">&nwarr;&nbsp;</span><?php
                            endif ?>
                            <span class="button bg_button cr_move">&udhar;</span>
                            <span data-type="edit" class="button bg_note cr_pointer indent">&rarrap;</span>
                            <b><?php echo $aAccess['title'] ?></b>
                        </a>
                        <div class="db-access_action hidden"><?php
                            $aAccessAction = array();
                            /* @var $oAction \Data\Doctrine\Main\AccessAction */
                            foreach($aAccess['pages'] as $oAction):
                                $aAccessAction[ $oAction->getAction()->getId() ] = array(
                                    $oAction->getPermission(), !$oAction->getPermission(), $oAction->getAccess()->getId()//, $oAction->getId()
                                );
                            endforeach;
                            echo json_encode($aAccessAction);
                        ?></div><?php
                        if ($aAccess['sub']):
                            $this->evalFunction('new', array());
                            foreach ($aAccess['sub'] as $idChild):
                                $this->evalFunction('circle', array($idChild, $aData[$idChild]));
                            endforeach;
                        else:
                            $this->evalFunction('new', array());
                        endif; ?>
                    </li>
                </ul><?php
            };
            $this->regFunction('circle', $fCircle);

            $fNew();
            foreach ($aData as $id => $aAccess):
                if ($aAccess['child']):
                    continue;
                endif;
                $fCircle($id, $aAccess);
            endforeach;
            ?>
            </div>
            <div class="el_grid_top el_table ui" data-class="View/Height" data-actions="addHiddenClass">
                <p class="indent bg_button">
                    <span class="right co_attention" id="access_name"></span>
                    <?php echo $translate->sys( 'LB_ACCESS_ACTION_LIST' ) ?>
                </p>
                <div class="indent el_grid_normalized el_width">
                    <span><?php echo $translate->sys( 'LB_BUTTON_ACCESS' ) ?></span>
                    <span><?php echo $translate->sys( 'LB_ACCESS_ACTION' ) ?></span>
                    <span><?php echo $translate->sys( 'LB_BUTTON_DENY' ) ?></span>
                </div>
                <div class="db-action el_scroll">
                    <?php
                    /* @var $oAction \Data\Doctrine\Main\Action */
                    foreach ($this->get('url_list') as $oAction):
                        ?><table class="inactive el_width_full" data-id="<?php echo $oAction->getId() ?>">
                            <tr>
                            <td width="70px" class="center">
                                <input name="allow" type="checkbox" />
                            </td>
                            <td>
                                <span>
                                    <strong><a target="_blank" href="<?php echo $this->getUrl( $oAction->getUrl() ) ?>"><?php echo $oAction->getUrl() ?></a></strong>
                                    <span>&origof;</span>
                                    <span class="co_attention"><?php echo $oAction->getAction() ?></span>
                                </span>
                            </td>
                            <td width="30px" class="center">
                                <input name="deny" type="checkbox" />
                            </td>
                            </tr>
                        </table><?php
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
        <p>&nbsp;</p>
        <div class="db-submit button bg_attention"><?php echo $translate->sys( 'LB_BUTTON_APPLY' ) ?></div>
        <p>&nbsp;</p>

        <div class="hidden" id="add_new">
            <li class="el_width">
                <div class="button bg_button cr_default el_grid el_width">
                    <input type="text" class="ui-delay" data-class="Modules/Dev/Access" data-actions="initNew" />
                </div>
            </li>
        </div>

        <div class="hidden" id="elem_new">
            <ul class="list ui-delay" data-class="View/DragDrop" data-actions="drop">
                <li class="db-access ui-delay" data-class="View/DragDrop" data-actions="drag" data-id="{ID}" data-title="{TITLE}">
                    <a class="button bg_button cr_default ui-delay" href="#" data-class="View/Href" data-actions="preventDefault">
                        <span class="button right bg_attention cr_pointer ui-delay" data-class="View/Height" data-actions="remove" data-target="closest:li:">&Cross;</span>
                        <span class="button bg_button cr_move">&udhar;</span>
                        <span data-type="edit" class="button bg_note cr_pointer indent">&rarrap;</span>
                        <b>{TITLE}</b>
                    </a>
                    <div class="db-access_action hidden">{}</div>
                    <ul class="list ui-delay" data-class="View/DragDrop" data-actions="drop">
                        <li><div class="indent cr_default"><span class="db-add button cr_pointer">&erarr;</span> <?php echo $translate->sys('LB_ACCESS_ADD_NEW') ?></div></li>
                    </ul>
                </li>
            </ul>
        </div>
    </section>
</article>
