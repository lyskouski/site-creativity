<?php namespace Modules\Person\Work\Poetry;

/**
 * General controller for index page
 * @see \Modules\AbstractController
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Log
 */
class Controller extends \Modules\Person\Work\Article\Controller
{
    /**
     * @var integer - main templat position
     */
    protected $pos = 1;

    /**
     * Has to be declared for other classes
     * @return Model
     */
    protected function getModel()
    {
        $modelName = str_replace('\\Controller', '\\Model', get_class($this));
        return (new $modelName);
    }

    public function indexNumAction(array $aParams)
    {
        $oHelper = parent::indexNumAction($aParams);
        $oTemplate = $oHelper->get($this->pos);
        if (strpos($oTemplate->getTemplate(), 'Article') || strpos($oTemplate->getTemplate(), 'Editor') === 0) {
            $oTemplate->changeTemplate("Entity/Poetry/create");
        }
        return $oHelper;
    }

}
