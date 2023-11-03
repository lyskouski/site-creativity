<?php namespace Modules\Mind\Trainer\Gibberish;

/**
 * Mind Trainer (controller): Gibberish
 *
 * @since 2016-12-26
 * @author Viachaslau Lyskouski
 */
class Controller extends \Modules\Mind\Trainer\AbstractController
{
    public function getModel()
    {
        return new Model();
    }

}
