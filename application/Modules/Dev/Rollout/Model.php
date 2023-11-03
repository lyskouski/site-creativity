<?php namespace Modules\Dev\Rollout;

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
     * @var string
     */
    protected $dir;

    /**
     * @var \Engine\Request\Helper\Mercurial
     */
    protected $hg;

    /**
     * Data initialization
     */
    public function __construct()
    {
        $this->dir = \System\Registry::config()->getReleaseDir();
        $this->hg = new \Engine\Request\Helper\Mercurial($this->dir . '/creativity_hg');
    }

    /**
     *
     * @return \Engine\Request\Helper\Mercurial
     */
    public function getMercurial()
    {
        return $this->hg;
    }

    /**
     * Get the latest list of commited changes
     *
     * @return array<\Data\Doctrine\Main\Released>
     */
    public function getRelease()
    {
        $limit = 25;
        $em = \System\Registry::connection();
        // Check current
        $actual = $em->getRepository(CrMain::RELEASED)->findBy(
            array(),
            array('updatedAt' => 'DESC'),
            $limit * 2
        );
        $ids = array();
        $active = current($actual);
        /* @var $o \Data\Doctrine\Main\Released */
        foreach ($actual as $o) {
            $ids[] = (int) $o->getVersion();
        }

        if (!$active || $active->getVersion() !== $this->getVersion()) {
            $rels = array();
            foreach ($this->hg->getLog($limit) as $a) {
                if (in_array((int) $a['id'], $ids)) {
                    continue;
                }
                preg_match('/(#)(\d{1,})/', $a['title'], $matches);
                $rel = new \Data\Doctrine\Main\Released();
                $rel->setVersion($a['version'])
                        ->setBranch(\Defines\Database\Branch::ALPHA)
                        ->setUpdatedAt($a['date'])
                        ->setDescription($a['title']);
                if (sizeof($matches) >= 3) {
                    $rel->setContent($em->getReference(CrMain::CONTENT, $matches[2]));
                }
                $em->persist($rel);
                $rels[] = $rel;
            }
            // Join with a database list
            if ($rels) {
                $em->flush();
                foreach ($actual as $o) {
                    $rels[] = $o;
                }
                $actual = $rels;
            }
        }

        return $actual;
    }

    public function getReleasedLive()
    {
        /* @var $actual \Data\Doctrine\Main\ReleasedLive */
        $actual = \System\Registry::connection()->getRepository(CrMain::RELEASED_LIVE)->findOneByActive(true);
        $result = '1.0.0';
        if ($actual) {
            $result = $actual->getVersion();
        }
        return $result;
    }

    public function getLiveList()
    {
        return \System\Registry::connection()->getRepository(CrMain::RELEASED_LIVE)->findBy([], array(
            'updatedAt' => 'DESC'
        ), 5);
    }

    public function createLiveRelease($version, $title)
    {
        $em = \System\Registry::connection();
        // Check the last one
        $actual = $em->getRepository(CrMain::RELEASED)->findOneBy(
            array('branch' => \Defines\Database\Branch::BETA),
            array('id' => 'DESC')
        );
        // Create new release
        $release = new \Data\Doctrine\Main\ReleasedLive();
        $release->setVersion($version)
            ->setDescription($title)
            ->setUpdatedAt(new \DateTime)
            ->setReleased($actual);
        $em->persist($release);
        $em->flush();

        $folder = $this->dir . '/creativity_release/' . $release->getId();
        shell_exec("cp -R {$this->dir}/creativity_test {$folder}");
        $this->clearCache();
    }

    public function setLive($id)
    {
        // Check folder
        $folder = $this->dir . '/creativity_release/' . $id;
        if (!is_dir($folder)) {
            throw new \Error\Validation("Targe folder $folder is missing");
        }

        // Update entity
        $em = \System\Registry::connection();
        // Disable other versions
        $em->createQuery(
                "UPDATE \Data\Doctrine\Main\ReleasedLive AS rl
                SET rl.active = 0
                WHERE rl.active = 1"
            )
            ->execute();
        /* @var $actual \Data\Doctrine\Main\ReleasedLive */
        $actual = $em->find(CrMain::RELEASED_LIVE, $id);
        $actual->setActive(true);
        $em->persist($actual);
        $em->flush();

        shell_exec("cd {$this->dir}; rm creativity_main; ln -sf ./creativity_release/{$id} creativity_main");
        $this->clearCache();
    }

    /**
     * Clear php-cache
     */
    public function clearCache()
    {
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        // Clear APC for symlinks
        if (function_exists('apc_clear_cache')) {
            apc_clear_cache();
            apc_clear_cache('user');
            apc_clear_cache('opcode');
        }
        // Clear Doctrine ORM cache
        $ormConfig = \System\Registry::connection()->getConfiguration();
        if ($ormConfig->getMetadataCacheImpl()) {
            $ormConfig->getMetadataCacheImpl()->deleteAll();
        }
        if ($ormConfig->getResultCacheImpl()) {
            $ormConfig->getResultCacheImpl()->deleteAll();
        }
        if ($ormConfig->getQueryCacheImpl()) {
            $ormConfig->getQueryCacheImpl()->deleteAll();
        }
        \System\Registry::connection()->clear();
    }

    /**
     * Get actual Hg release version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->hg->getVersion();
    }

    public function getChanges()
    {
        return trim($this->hg->getStat());
    }

    /**
     * Check version notes
     *
     * @param string $path - dir name
     * @return string
     */
    protected function getReleaseNotes($path)
    {
        $filename = "{$this->dir}/$path/version.txt";
        $version = \System\Registry::translation()->sys('LB_HEADER_404');
        if (file_exists($filename)) {
            $version = file_get_contents($filename);
        }
        return $version;
    }

    /**
     * Get actual version for alpha-version
     *
     * @return string
     */
    public function getVersionAlpha()
    {
        return $this->hg->getSummary();
    }

    /**
     * Get actual version for beta-version
     *
     * @return string
     */
    public function getVersionBeta()
    {
        return $this->getReleaseNotes('creativity_test');
    }

    /**
     * Get actual version for LIVE-version
     *
     * @return string
     */
    public function getVersionLive()
    {
        return $this->getReleaseNotes('creativity_main');
    }

    public function getTestsPath()
    {
        return $this->dir . '/creativity_hg/documents/reports/phpunit.json';
    }

    public function getTestsResult()
    {
        $content = str_replace('}{', '},{', file_get_contents($this->getTestsPath()));
        $json = new \Engine\Request\Json("[$content]");
        $result = array();
        foreach ($json as $a) {
            if (isset($a['status']) && $a['status'] != 'pass') {
                $result[] = $a;
            }
        }
        return $result;
    }

    public function runPhing($type = 'main')
    {
        ignore_user_abort(true);
        set_time_limit(0);

        $log = (new \System\Shell)->run("cd {$this->dir}/creativity_hg;php vendor/bin/phing $type");

        /* @var $rep \Data\Model\Released */
        $rep = \System\Registry::connection()->getRepository(CrMain::RELEASED);
        switch ($type) {
            case 'main':
                $rep->updateBranch(\Defines\Database\Branch::BETA, $this->getVersion());
                break;

            case 'test':
                if (!$this->getTestsResult()) {
                   $rep->updateTests($this->getVersion());
                }
                break;
        }
        $this->clearCache();
        return $log;
    }

}
