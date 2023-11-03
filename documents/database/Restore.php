<?php

use Data\Doctrine\Main;
/**
 * Functions to provide a migration an old information to a new structure
 */
class Restore
{

    public function initOld()
    {
        $database = new \mysqli('', '', '');
        $database->select_db('creativi_main');
        $database->set_charset('utf8');
        $this->db = $database;
    }


    // @note descriptions
    public function descriptionAction(array $aParams)
    {
        $oConv = new \System\Converter\String();
        $help = new \Data\ContentHelper();
        $em = $help->getEntityManager();
        $aResult = $help->getRepository()->findBy(array(
            'type' => 'description'
        ));
        foreach ($aResult as $entity) {
            $s = strip_tags(str_replace("\n", ' ', $entity->getContent()));
            if ($oConv->strlen($s) > 150) {
                $temp = $oConv->substr($s, 0, 150);
                $i = strripos($temp, ' ');
                $s = $oConv->substr($temp, 0, $i ? $i : 145) . '...';
            }
            $entity->setContent($s);
            $entity->setSearch($s);
            $em->persist($entity);
            echo $entity->getPattern(), '<br />';
        }
        $em->flush();
        die;
    }

// @note topic counts
    public function countsAction(array $aParams)
    {
        $oResult = $this->db->query("SELECT COUNT(*) cnt, pattern FROM cr_main.content WHERE pattern LIKE 'dev/offtopic/i%' AND type LIKE 'content#%' GROUP BY pattern");
        $aResult = $oResult->fetch_all(MYSQLI_ASSOC);

        $rep = (new \Data\ContentHelper)->getRepository();
        $oDb = \System\Registry::connection();
        foreach ($aResult as $a) {
            $oStat = $rep->getPages($a['pattern']);
            $oStat->setContentCount($a['cnt']);
            $oDb->persist($oStat);
            echo "{$a['pattern']} -> {$a['cnt']}<br />";
        }
        $oDb->flush();
        die;
    }

// @note forum import
    public function forumAction(array $aParams)
    {
        set_time_limit(0);
        $oResult = $this->db->query("select c.id, t.id as tid, t.info, r.info AS rzd, c.usr, c.date, c.text
            from creativi_db.fm_comment AS c
            inner join creativi_db.fm_topic AS t ON c.id_top = t.id
            left join creativi_db.fm_razdel AS r ON r.id = t.id_rzd
            order by tid, c.id");
        $aResult = $oResult->fetch_all(MYSQLI_ASSOC);
        $tid = -1;
        $i = 0;
        $id = 0;
        $oDb = \System\Registry::connection();
        foreach ($aResult as $a) {
            $oAuthor = $oDb->getRepository(\Defines\Database\CrMain::USER)->findOneBy(array('username' => $a['usr']));
            $pattern = 'dev/offtopic/i' . $id;
            $a['date'] = explode('-', $a['date']);
            if (!isset($a['date'][1])) {
                $a['date'][1] = '03:00:00';
            }
            $a['date'] = date('Y-m-d H:i:s', strtotime(str_replace('.', '-', $a['date'][0]) . ' ' . str_replace('.', ':', $a['date'][1])));

            if ($tid != $a['tid']) {
                $tid = $a['tid'];
                $i = 0;

                $oTitle = new Main\Content();
                $oTitle->setLanguage('ru')
                    ->setAuthor($oAuthor)
                    ->setPattern('dev/offtopic/i')
                    ->setType('og:title')
                    ->setContent($a['info'])
                    ->setSearch(strip_tags($a['info']))
                    ->setUpdatedAt(new \DateTime($a['date']));
                $oDb->persist($oTitle);
                $oDb->flush();

                $id = $oTitle->getId();
                $pattern = 'dev/offtopic/i' . $id;
                $oTitle->setPattern($pattern);
                $oDb->persist($oTitle);

                $oDescription = new Main\Content();
                $oDescription->setLanguage('ru')
                    ->setAuthor($oAuthor)
                    ->setPattern($pattern)
                    ->setType('description')
                    ->setContent(strip_tags($a['text']))
                    ->setUpdatedAt(new \DateTime($a['date']));
                $oDb->persist($oDescription);

                $oKeywords = new Main\Content();
                $oKeywords->setLanguage('ru')
                    ->setAuthor($oAuthor)
                    ->setPattern($pattern)
                    ->setType('keywords')
                    ->setContent($a['rzd'])
                    ->setUpdatedAt(new \DateTime($a['date']));
                $oDb->persist($oKeywords);
            }

            $oTopic = new Main\Content();
            $oTopic->setLanguage('ru')
                ->setAuthor($oAuthor)
                ->setPattern($pattern)
                ->setType('content#' . $i++)
                ->setContent($a['text'])
                ->setSearch(strip_tags($a['text']))
                ->setUpdatedAt(new \DateTime($a['date']));
            $oDb->persist($oTopic);
            $oDb->flush();

            echo "[$id] $i - {$a['info']}<br />";
        }
        exit;
    }

// @note comments import

    public function commentsAction(array $aParams)
    {
        set_time_limit(0);
        $oDb = \System\Registry::connection();

// Add missing users
        $oResult = $this->db->query("SELECT DISTINCT user FROM creativi_main.topics_comment");
        $aResult = $oResult->fetch_all(MYSQLI_ASSOC);
        foreach ($aResult as $a) {
            $oAuthor = $oDb->getRepository(\Defines\Database\CrMain::USER)->findOneBy(array('username' => $a['user']));
            if (!$oAuthor) {
                $oAuthor = new Main\User();
                $oAuthor->setUsername($a['user']);
                $oDb->persist($oAuthor);
            }
        }
        $oDb->flush();
        die;

// Add comments
        $oResult = $this->db->query("
            SELECT tc.uni, tc.user as author, tc.ti_ini, t.user AS user, t.link, c.pattern, c.language
            FROM creativi_main.topics_comment AS tc
            INNER JOIN creativi_main.topics AS t ON t.id = tc.id
            LEFT JOIN cr_main.user AS u ON u.username = t.user
            LEFT JOIN cr_main.content AS c ON c.content = t.topic AND u.id = c.author_id
        ");
        $aResult = $oResult->fetch_all(MYSQLI_ASSOC);
        foreach ($aResult as $a) {
            $sPath = realpath(\System\Registry::config()->getAppPath() . '/../../../creativity_data/_old/data/');
            $sDir = '/' . $a['user'] . '/' . $a['link'];
            if (!realpath($sPath . $sDir)) {
                $sDir = '/' . str_replace(' ', '_', $a['user']) . '/' . $a['link'];
            }
            $sPath .= $sDir . '/' . $a['uni'] . '.comm';
            if (!file_exists($sPath)) {
                continue;
            }
            $sText = file_get_contents($sPath);

            $aCnt = $this->db->query("SELECT COUNT(*) AS cnt FROM cr_main.content WHERE pattern='{$a['pattern']}' AND language = '{$a['language']}' AND type LIKE 'comment#%' ")->fetch_all(MYSQLI_ASSOC);

            $oAuthor = $oDb->getRepository(\Defines\Database\CrMain::USER)->findOneBy(array('username' => $a['author']));
            $oTitle = new Main\Content();
            $oTitle->setLanguage($a['language'])
                ->setAuthor($oAuthor)
                ->setPattern($a['pattern'])
                ->setType('comment#' . ++$aCnt[0]['cnt'])
                ->setContent($sText)
                ->setUpdatedAt(new \DateTime($a['ti_ini']));
            $oDb->persist($oTitle);
            $oDb->flush();

            echo '<hr>', $a['author'], ': ', $sText;
        }


        die;
    }

//  @note predefined keywords - done
    public function keywordsAction()
    {
        set_time_limit(0);
        $oResult = $this->db->query("
      SELECT c.pattern, c.language, c.updated_at, c.access, c.author_id, t.mrk
      FROM cr_main.content AS c
      LEFT JOIN cr_main.user ON user.id = c.author_id
      LEFT JOIN creativi_main.topics AS t ON t.topic = c.content AND user.username = t.user
      WHERE c.type='og:title' AND c.pattern LIKE 'oeuvre/%/i%'
      group by c.pattern, c.language, c.author_id");
        $aResult = $oResult->fetch_all(MYSQLI_ASSOC);

        $oDb = \System\Registry::connection();
        foreach ($aResult as $a) {
            $aKey = $this->db->query("SELECT
      tp0.ru key0, tp1.ru key1, tp2.ru key2, tp3.ru key3, tp4.ru key4
      FROM creativi_main.topics_pedigree tp0
      LEFT JOIN creativi_main.topics_pedigree tp1 ON tp1.mrk = tp0.nzg
      LEFT JOIN creativi_main.topics_pedigree tp2 ON tp2.mrk = tp0.zag
      LEFT JOIN creativi_main.topics_pedigree tp3 ON tp3.mrk = tp0.pzd
      LEFT JOIN creativi_main.topics_pedigree tp4 ON tp4.mrk = tp0.rzd
      WHERE tp0.mrk = {$a['mrk']}")->fetch_all(MYSQLI_ASSOC);
            $aKey = array_diff(array_unique(array_values($aKey[0])), array(''));

            $oImage = new Main\Content();
            $oAuthor = $oDb->getRepository(\Defines\Database\CrMain::USER)->find($a['author_id']);
            $oImage->setAccess($a['access'])
                ->setAuthor($oAuthor)
                ->setContent(implode(',', $aKey))
                ->setSearch(implode(',', $aKey))
                ->setLanguage($a['language'])
                ->setPattern($a['pattern'])
                ->setType('keywords')
                ->setUpdatedAt(new \DateTime($a['updated_at']));
            $oDb->persist($oImage);
        }
        $oDb->flush();
        die('ok');
    }

//  @note predefined images - done
    public function imagesAction()
    {
        set_time_limit(0);
        $oResult = $this->db->query("SELECT pattern, language, updated_at, access, author_id FROM cr_main.content WHERE type='og:title' AND pattern LIKE 'oeuvre/%/i%'");
        $aResult = $oResult->fetch_all(MYSQLI_ASSOC);

        $oDb = \System\Registry::connection();
        foreach ($aResult as $a) {
            $s = $a['pattern'];
            $m = explode('/', $s);

            $oImage = new Main\Content();
            $oAuthor = $oDb->getRepository(\Defines\Database\CrMain::USER)->find($a['author_id']);
            $oImage->setAccess($a['access'])
                ->setAuthor($oAuthor)
                ->setContent('/img/css/el_notion/work/' . $m[1] . '.svg')
                ->setLanguage($a['language'])
                ->setPattern($s)
                ->setType('og:image')
                ->setUpdatedAt(new \DateTime($a['updated_at']));
            $oDb->persist($oImage);
        }
        $oDb->flush();
        die('ok');
    }

//  @note fix dates - done
    public function dateAction()
    {
        set_time_limit(0);
        $oResult = $this->db->query("SELECT MIN(updated_at) updated_at, pattern FROM cr_main.content WHERE pattern LIKE 'oeuvre/%/i%' GROUP BY pattern");
        $aResult = $oResult->fetch_all(MYSQLI_ASSOC);
        foreach ($aResult as $a) {
            $this->db->query("UPDATE cr_main.content SET updated_at = '{$a['updated_at']}' WHERE pattern = '{$a['pattern']}'");
        }
    }

//  @note publication import - done
    public function restoreAction(array $aParams)
    {
        set_time_limit(0);
        $oResult = $this->db->query("SELECT * FROM creativi_main.topics LIMIT 800, 100");
        $aResult = $oResult->fetch_all(MYSQLI_ASSOC);
        $oDb = \System\Registry::connection();

        foreach ($aResult as $a) {
            $sLink = 'oeuvre/';
            switch ($a['type']) {
                case 'verse':
                    $sLink .= 'poetry';
                    break;
                default:
                    $sLink .= 'prose';
                    break;
            }
            $oAuthor = $oDb->getRepository(\Defines\Database\CrMain::USER)->findOneBy(array('username' => $a['user']));
            if (!$oAuthor) {
                $oAuthor = new Main\User();
                $oAuthor->setUsername($a['user']);
                $oDb->persist($oAuthor);
                $oDb->flush();
            }

            $oTitle = new Main\Content();
            $oTitle->setLanguage($a['lang'])
                ->setAuthor($oAuthor)
                ->setPattern($sLink)
                ->setType('og:title')
                ->setContent($a['topic'])
                ->setSearch($a['topic'])
                ->setUpdatedAt(new \DateTime($a['ti_ini']));
            $oDb->persist($oTitle);
            $oDb->flush();

            $sLink .= '/i' . $oTitle->getId();
// Update link
            $oTitle->setPattern($sLink);
            $oDb->persist($oTitle);
            $oDb->flush();

            $iCount = 0;

            $sPath = realpath(\System\Registry::config()->getAppPath() . '/../../../creativity_data/_old/data/');
            $sDir = '/' . $a['user'] . '/' . $a['link'];
            if (!realpath($sPath . $sDir)) {
                $sDir = '/' . str_replace(' ', '_', $a['user']) . '/' . $a['link'];
            }
            $sPath .= $sDir;

            foreach (scandir($sPath) as $s) {
                $sFile = $sPath . '/' . $s;
                $oContent = new Main\Content();
                $oContent->setLanguage($a['lang'])
                    ->setAuthor($oAuthor)
                    ->setPattern($sLink);

                if (strpos($s, '.desc')) {
                    $s = file_get_contents($sFile);
                    $oContent->setType('description')
                        ->setContent($s)
                        ->setSearch(strip_tags($s))
                        ->setUpdatedAt(new \DateTime($a['ti_ini']));
                } elseif (strpos($s, '.page')) {
                    $s = file_get_contents($sFile);
                    $oContent->setType('content#' . $iCount)
                        ->setContent($s)
                        ->setSearch(strip_tags($s))
                        ->setUpdatedAt(new \DateTime($a['ti_ini']));
                    $iCount++;
                } else {
                    continue;
                }
                $oDb->persist($oContent);
            }
            $oDb->flush();


            $votesUp = ceil(($a['rejt_num'] * $a['rejt'] / 5) / (1 + 2 * $a['rejt'] / 5 ));
            $votesDown = $a['rejt_num'] - $votesUp;

            /* @var $oRating \Data\Doctrine\Main\ContentViews */
            $oRating = $oDb->getRepository(\Defines\Database\CrMain::CONTENT_VIEWS)->find($oTitle->getId());
            $oRating->setVisitors($a['looks'])
                ->setContentCount($iCount)
                ->setVotesUp($votesUp)
                ->setVotesDown($votesDown);
            $oDb->persist($oRating);
            $oDb->flush();

            echo '<br>' . $a['topic'] . "($votesUp / $votesDown)";
        }
        exit;
    }

//  @note User import - done
    public function usersAction()
    {
        $oResult = $this->db->query("SELECT * FROM creativi_main.users AS u
      INNER JOIN creativi_main.users_pid AS up
      WHERE u.user = up.user");
        $aResult = $oResult->fetch_all(MYSQLI_ASSOC);

        $oDb = \System\Registry::connection();
        $aUniq = array();
        foreach ($aResult as $a) {
            $s = trim($a['user']);

            if (in_array($s, $aUniq)) {
                $oUser = $oDb->getRepository(\Defines\Database\CrMain::USER)->findOneBy(array('username' => $s));
            } else {
                $aUniq[] = $s;
                $oUser = new Main\User();
                $oUser->setUsername($s);
                $oDb->persist($oUser);

                $oUAccess = new Main\UserAccess();
                $oUAccess->setUser($oUser);
                $oUAccess->setAccess($oDb->getRepository(\Defines\Database\CrMain::ACCESS)->find(\Defines\Users::AUTHOR));
                $oDb->persist($oUAccess);

                $oUPhone = new Main\UserAccount();
                $oUPhone->setUser($oUser);
                $oUPhone->setAccount($a['phone']);
                $oUPhone->setType(\Defines\User\Account::VIBER);
                $oUPhone->setUpdatedAt(new \DateTime);
                $oDb->persist($oUPhone);
            }

            $oUProfile = new Main\UserAccount();
            $oUProfile->setUser($oUser);
            $oUProfile->setAccount($a['pid']);
            $oUProfile->setType(\Defines\User\Account::MAIL);
            $oUProfile->setUpdatedAt(new \DateTime);
            $oDb->persist($oUProfile);

            echo '<br>' . $a['user'];
            $oDb->flush();
        }

        echo '<hr>Added users: ' . sizeof($aResult);
        exit;
    }
}
