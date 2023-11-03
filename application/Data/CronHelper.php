<?php

namespace Data;

use Defines\Database\CrMain;

/**
 * User management helper
 *
 * @author Viachaslau Lyskouski
 * @since 2015-09-21
 * @package Data
 */
class CronHelper extends HelperAbstract
{

    protected function getTarget()
    {
        return CrMain::CRON;
    }

    /**
     * Get first 10 mails from a list
     * 
     * @param type $iLimit
     * @return array<\Data\Doctrine\Main\CronTaskMail>
     */
    public function getMailTasks($iLimit = 10)
    {
        $oManager = $this->getEntityManager();
        $aList = $oManager->getRepository(CrMain::CRON_TASK_MAIL)
                ->findBy(array('status' => null), array(), $iLimit);
        return $aList;
    }

}
