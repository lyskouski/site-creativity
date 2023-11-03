<?php namespace Modules\Dev\Board;

use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * Model object for index page
 *
 * @since 2015-05-22
 * @author Viachaslau Lyskouski
 * @package Modules/Index
 */
class Model extends \Modules\AbstractModel
{

    public function getNewTasks()
    {
        $em = \System\Registry::connection();
        $list = $em->createQuery(
            "SELECT c
            FROM Data\Doctrine\Main\Content c
            LEFT JOIN Data\Doctrine\Main\Content AS c2 WITH c2.pattern = c.pattern AND c2.type = 'task'
            WHERE
                (c.pattern LIKE 'dev/bugs/i%' OR c.pattern LIKE 'dev/proposition/i%')
                AND c.type = 'og:title'
                AND c2.id IS NULL"
        )->getResult();

        return $em->getRepository(\Defines\Database\CrMain::CONTENT)->prepareData($list);
    }

    public function getQueryTask($access, $limit = false, $offset = 0)
    {
        $em = \System\Registry::connection();
        $query = $em->createQuery(
            "SELECT c
            FROM Data\Doctrine\Main\Content c
            WHERE
                (c.pattern LIKE 'dev/bugs/i%' OR c.pattern LIKE 'dev/proposition/i%')
                AND c.type = 'task'
                AND c.access = :access
            ORDER BY c.updatedAt DESC"
            )
            ->setParameter('access', $access);
        if ($limit) {
            $query->setMaxResults($limit)
                ->setFirstResult($offset * $limit);
        }
        return $query;
    }

    public function getTasks($type, $limit = false)
    {
        $em = \System\Registry::connection();
        $query = $this->getQueryTask($type, $limit);
        return $em->getRepository(\Defines\Database\CrMain::CONTENT)->prepareData($query->getResult());
    }

    public function getPageTasks($type, $numPage, $title, $url)
    {
        $query = $this->getQueryTask($type, \Defines\Database\Params::CONTENT_ON_PAGE, $numPage);
        $page = new Paginator($query);

        return array(
            'title' => $title,
            'curr' => $numPage,
            'num' => \Defines\Database\Params::getPageCount($page->count(), \Defines\Database\Params::CONTENT_ON_PAGE),
            'url' => $url,
            'list' => \System\Registry::connection()->getRepository(\Defines\Database\CrMain::CONTENT)->prepareData($page->getQuery()->getResult())
        );
    }


    public function changeStatus($pattern, $type)
    {
        $em = \System\Registry::connection();
        $task = $em->getRepository(\Defines\Database\CrMain::CONTENT)->findOneBy([
            'type' => 'task',
            'pattern' => $pattern
        ]);
        $user = \System\Registry::user()->getEntity();
        if (!$task) {
            $task = new \Data\Doctrine\Main\Content();
            $task->setAuthor($user)
                ->setPattern($pattern)
                ->setType('task')
                ->setLanguage(\System\Registry::translation()->getTargetLanguage());
        }
        $task->setAuditor($user)
            ->setAccess($type)
            ->setContent($type)
            ->setUpdatedAt(new \DateTime);
        $em->persist($task);
        $em->flush();
    }

    public function changeSubtask($list)
    {
        $em = \System\Registry::connection();
        foreach ($list as $id => $access) {
            $subtask = $em->find(\Defines\Database\CrMain::CONTENT, $id);
            if (strpos($subtask->getType(), 'subtask#') !== 0) {
                throw new \Error\Validation('Incorrect subtask');
            }
            if (!in_array($access, \Defines\Database\BoardCategory::getList())) {
                throw new \Error\Validation('Incorrect type for subtask');
            }
            $subtask->setAccess($access);
            $em->persist($subtask);
        }
        $em->flush();
    }

    public function getSubtaskList()
    {
        $list = $this->getQueryTask(\Defines\Database\BoardCategory::ACTIVE)->getResult();
        $patternList = array();
        foreach ($list as $o) {
            $patternList[] = $o->getPattern();
        }

        $em = \System\Registry::connection();
        return $em->createQuery(
            "SELECT c
            FROM Data\Doctrine\Main\Content c
            WHERE
                (c.type LIKE 'subtask#%')
                AND c.pattern IN (:pattern)
            GROUP BY c.pattern, c.type
            ORDER BY c.pattern ASC, c.access DESC, c.content ASC"
            )
            ->setParameter('pattern', $patternList)->getResult();
    }
}
