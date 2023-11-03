<?php namespace Modules\Dev\History;

/**
 * Model object for index page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\AbstractModel
{
    /**
     * Get publication date
     * @param \Data\Doctrine\Main\Content $data
     * @return string - date format
     */
    public function getFirstDate(\Data\Doctrine\Main\Content $data)
    {
        $date = $data->getUpdatedAt()->format(\Defines\Database\Params::DATE_FORMAT);

        $em = \System\Registry::connection();
        $oHistory = $em->getRepository(\Defines\Database\CrMain::CONTENT_HISTORY)->findOneBy(array(
            'pattern' => $data->getPattern(),
            'type' => 'og:title',
            'authorId' => $data->getAuthor() ? $data->getAuthor()->getId() : null
        ), array(
            'updatedAt' => 'ASC'
        ));
        if ($oHistory) {
            $date = $oHistory->getUpdatedAt()->format(\Defines\Database\Params::DATE_FORMAT);
        }
        return $date;
    }

    /**
     * Get history of content changes
     *
     * @param integer $id
     * @return array
     */
    public function getHistory($id)
    {
        $em = \System\Registry::connection();
        $oComment = $em->find(\Defines\Database\CrMain::CONTENT, $id);
        $oTranslate = \System\Registry::translation();
        if (!$oComment) {
            throw new \Error\Validation($oTranslate->sys('LB_ERROR_INCORRECT_REQUEST'));
        }
        $aHistory = $em->getRepository(\Defines\Database\CrMain::CONTENT_HISTORY)->findBy(array(
                'id' => $oComment->getId()
            ), array(
                'updatedAt' => 'DESC'
            ),
            \Defines\Database\Params::COMMENTS_ON_PAGE
        );

        return array(
            'current' => $oComment,
            'list' => $aHistory
        );
    }
}
