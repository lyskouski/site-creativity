<?php namespace Modules\Dev\Group;

use Data\Doctrine\Main\Access;
use Defines\Database\CrMain;

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
     * @var \Doctrine\ORM\EntityManager
     */
    protected $db;

    /**
     * Get locked access profiles
     *
     * @return array
     */
    public function getLocked()
    {
        $list = array(
            'LB_ACCESS_ADMIN',
            'LB_ACCESS_OTHER'
        );
        if (!\System\Registry::user()->isAdmin()) {
            $list[] = 'LB_ACCESS_NEW_EDITOR';
        }
        return $list;
    }

    /**
     * Init database conection
     */
    public function __construct()
    {
        $this->db = \System\Registry::connection();
    }

    /**
     * Get roles
     *
     * @return array<\Data\Doctrine\Main\Access>
     */
    public function getUserTypes()
    {
        return $this->db->getRepository(CrMain::ACCESS)->findBy([], array(
            'title' => 'ASC'
        ));
    }

    /**
     * Get counts for groups
     * @return array
     */
    public function getGroupsCount()
    {
        $query = $this->db->createQuery(
            "SELECT COUNT(au.id) AS cnt, IDENTITY(au.access) AS id
            FROM \Data\Doctrine\Main\UserAccess AS au
            GROUP BY au.access"
        );
        $result = new \System\ArrayUndef();
        $result->setUndefined(0);
        foreach ($query->getResult() as $a) {
            $result[$a['id']] = $a['cnt'];
        }
        return $result;
    }

    /**
     * Get list of users with such role
     *
     * @param integer $id
     * @return \Data\Doctrine\Main\Access
     */
    public function getAccessById($id)
    {
        return $this->db->find(CrMain::ACCESS, $id);
    }

    /**
     * Get list of users with such role
     *
     * @param Access $access
     * @return array<\Data\Doctrine\Main\UserAccess>
     */
    public function getUsersByRole(Access $access)
    {
        return $this->db->getRepository(CrMain::USER_ACCESS)->findBy(
            array(
                'access' => $access
            ), array(
                'id' => 'DESC'
            )
        );
    }

    public function removePrivilegue($id)
    {
        /* @var $entity \Data\Doctrine\Main\UserAccess */
        $entity = $this->db->find(CrMain::USER_ACCESS, $id);
        if (in_array($entity->getAccess()->getTitle(), $this->getLocked())) {
            throw new \Error\Validation('Cannot be deleted');
        }
        $this->db->remove($entity);
        $this->db->flush();
    }

    /**
     * Add privilegues to user
     *
     * @param string $username
     * @param integer $id
     */
    public function addPrivilegue($username, $id)
    {
        /* @var $user \Data\Doctrine\Main\User */
        $user = $this->db->getRepository(CrMain::USER)->findOneByUsername($username);
        $access = $this->getAccessById($id);
        if (!($user || $access)) {
            throw new \Error\Validation('User is missing');
        }
        if (in_array($access->getTitle(), $this->getLocked())) {
            throw new \Error\Validation('Cannot be assigned to user');
        }
        $role = new \Data\Doctrine\Main\UserAccess();
        $role->setAccess($access)
            ->setUser($user);
        $this->db->persist($role);
        $this->db->flush();
    }
}
