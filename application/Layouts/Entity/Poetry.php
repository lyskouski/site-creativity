<?php namespace Layouts\Entity;

use Engine\Request\Params;

/**
 * Headers constants
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Layouts/Helper
 */
class Poetry extends \Layouts\Helper\Basic
{

    /**
     * Init layout helper-decorator
     * @note override header/footer path
     *
     * @param \Engine\Request\Params $oParams
     */
    public function __construct(Params $oParams, \Engine\Response $oResponse)
    {
        //$bInitial = $this->init;
        $this->init = 'Entity/Prose';
        parent::__construct($oParams, $oResponse);
        //$this->init = $bInitial;
    }

    public function getPageTemplate()
    {
        return 'Entity/Poetry/page';
    }

    /**
     * @see \Layouts\Helper\Basic::finalize
     * @override disable footer
     *
     * @return \Layouts\Entity\Prose
     */
    public function finalize()
    {
        return $this;
    }
}
