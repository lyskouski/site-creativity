<?php namespace Modules\Mind\Trainer\Gibberish;

/**
 * Mind Trainer (model): Gibberish
 *
 * @since 2016-12-26
 * @author Viachaslau Lyskouski
 */
class Model
{
    protected $typeRating = 'rating#';
    protected $typeGame = 'game#';

    /**
     * @var \Engine\Response\Translation
     */
    protected $tr;

    /**
     * @var string
     */
    protected $pattern = 'mind/trainer/gibberish';

    public function __construct()
    {
        $id = \System\Registry::user()->getEntity()->getId();
        $this->typeRating .= $id;
        $this->typeGame .= $id;
        $this->tr = \System\Registry::translation();
    }

    protected function get($type)
    {
        $em = \System\Registry::connection();
        $rep = $em->getRepository(\Defines\Database\CrMain::CONTENT);
        /* @var $entity \Data\Doctrine\Main\Conten */
        return $rep->findOneBy([
            'pattern' => $this->pattern,
            'type' => $type,
            'language' => \System\Registry::translation()->getTargetLanguage()
        ]);
    }

    public function getGameAttr()
    {
        $entity = $this->get($this->typeGame);
        $em = \System\Registry::connection();
        if (!$entity) {
            $lvl = trim($this->getGameRating()->getSearch());
            $data = $this->startGame($lvl);

            $entity = new \Data\Doctrine\Main\Content();
            $entity->setPattern($this->pattern)
                ->setType($this->typeGame)
                ->setAuthor(\System\Registry::user()->getEntity())
                ->setLanguage($this->tr->getTargetLanguage())
                ->setUpdatedAt(new \DateTime)
                ->setContent(json_encode($data));
            $em->persist($entity);
            $em->flush($entity);
        } else {
            $json = new \Engine\Request\Json($entity->getContent());
            if (!$json->isValid()) {
                $em->remove($entity);
                $em->flush($entity);
                throw new \Error\Validation('Broken Gibberish data. Actual game has been deleted!');
            }
            $data = $json->getArrayCopy();
        }

        return $data;
    }

    public function updateGame($idx, $replace = false)
    {
        $entity = $this->get($this->typeGame);
        if (!$entity) {
            throw new \Error\Validation('Broken Gibberish... game is missing');
        }
        $json = new \Engine\Request\Json($entity->getContent());
        if (!$json->isValid()) {
            throw new \Error\Validation('Broken Gibberish... invalid data');
        }

        if ($replace) {
            $json['stat'] = (array) $idx;
        } else {
            $json['stat'][] = $idx;
        }

        $entity->setContent(json_encode($json->getArrayCopy()));
        $em = \System\Registry::connection();
        $em->persist($entity);
        $em->flush($entity);
    }

    public function finalizeGame()
    {
        $rating = $this->getGameRating();
        $initRate = (int) $rating->getContent();
        $initLvl = $lvl = (int) $rating->getSearch();

        $em = \System\Registry::connection();

        // Get game stat
        $entity = $this->get($this->typeGame);
        $json = new \Engine\Request\Json($entity->getContent());
        if (!$json->isValid()) {
            $lvl = 0;
            $rate = -1;
        }
        $em->remove($entity);

        $json['stat'] = array_unique($json['stat']);

        $mistake = abs(sizeof($json['stat']) - $json['count']);
        $ok = 0;
        foreach ($json['stat'] as $key) {
            if (in_array($json['content'][(int) $key], $json['target'])) {
                $ok++;
            } else {
                $mistake++;
            }
        }

        if ($ok > $mistake && 100 * $mistake / $json['count'] < 10) {
            $lvl++;
        } elseif (100 * $mistake / $json['count'] > 40) {
            $lvl--;
            if ($lvl < 0) {
                $lvl = 0;
            }
        }

        $rate = sizeof($json['content']) * $ok / (1 + time() - $json['date']['start'] + 2 * $mistake);

        $rating->setContent($rate)
            ->setSearch($lvl);

        $em->persist($rating);
        $em->flush();

        return array(
            'rating' => $initRate,
            'rating_new' => $rate,
            'lvl' => $initLvl,
            'lvl_new' => $lvl,
            'mistakes' => $mistake,
            'mistakes_percent' => 100 * $mistake / $json['count'],
            'time_start' => date(\Defines\Database\Params::TIMESTAMP, $json['date']['start']),
            'time_finished' => (new \DateTime)->format(\Defines\Database\Params::TIMESTAMP),
            'time_left' => time() - $json['date']['start'] + $json['date']['left'],
            'data' => $json
        );
    }

    /**
     * @return \Engine\Request\Json
     */
    public function getGameRating()
    {
        $entity = $this->get($this->typeRating);
        if (!$entity) {
            $em = \System\Registry::connection();
            $entity = new \Data\Doctrine\Main\Content();
            $entity->setPattern($this->pattern)
                ->setType($this->typeRating)
                ->setAuthor(\System\Registry::user()->getEntity())
                ->setLanguage($this->tr->getTargetLanguage())
                ->setUpdatedAt(new \DateTime)
                ->setSearch(0) // Lvl
                ->setContent(0);// Rating
            $em->persist($entity);
            $em->flush($entity);
        }

        return $entity;
    }

    protected function startGame($lvl)
    {
        $list = array();
        // Prepare alphabet list
        $k = 0;
        $lang = \Defines\Language::getList();
        array_unshift($lang, $this->tr->getTargetLanguage());
        for ($i = 0; $i <= (int) $lvl; $i += 5) {
            if ($i && $i%5 === 0) {
                $k++;
            }
            if (!isset($lang[$k])) {
                break;
            }
            $tmp = preg_split('//u', $this->tr->sys('LB_ALPHABET', $lang[$k]), -1, PREG_SPLIT_NO_EMPTY);
            $chunk = round(sizeof($tmp)/5);
            $a = array_slice($tmp, $chunk * ($i - $k*5), $chunk);
            // @todo: language related labels [array_walk("char,language")]
            $list = array_unique(array_merge($list, $a));
        }

        $trg = array();
        $trgKey = array_rand($list, 1 + array_sum(array_map(function($v) {
            return (int) $v;
        }, preg_split('//u', $lvl))));
        foreach ((array) $trgKey as $key) {
            $trg[] = $list[$key];
        }

        $alphabet = array_values(array_diff($list, $trg));

        $data = array(
            'count' => 50 + 5 * substr($lvl, -1),
            'date' => array(
                'start' => time(),
                'left' => 500 - 10 * substr($lvl, 0, 1),
                'curr' => 1
            ),
            'target' => $trg,
            'content' => [],
            'stat' => []
        );
        // Fill missings
        $i = 0;
        while (sizeof($trg) < $data['count']) {
            $trg[] = $trg[$i];
            $i++;
        }

        // Fill alphabet
        $i = 0;
        while (sizeof($trg) + sizeof($alphabet) < 100 * (1 + (int) $lvl)) {
            $alphabet[] = $alphabet[$i];
            $i++;
        }

        $data['content'] = array_merge($trg, $alphabet);
        shuffle($data['content']);

        return $data;
    }

}
