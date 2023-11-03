<?php namespace Data\Model;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use Defines\Database\CrMain;
use Defines\User\Access;

/**
 * Helper for a book search
 *
 * @author Viachaslau Lyskouski
 * @since 2015-11-16
 * @package Data/Model
 */
class BookRepository extends EntityRepository
{
    const SEARCH_LIMIT = 8;

    public function searchList($criteria)
    {
        $list = array();

        // Find the direct book
        if (array_key_exists(\Engine\Book\Search::TYPE_ISBN, $criteria)) {
            $res = $this->find(ltrim($criteria[\Engine\Book\Search::TYPE_ISBN], '0'));
            if ($res) {
                $list[] = $res;
            }

        // Search by criterias
        } else {
            $prefix = 'b';
            $queryBuilder = $this->createQueryBuilder($prefix);
            foreach ($criteria as $key => $value) {
                if (!$value) {
                    continue;
                }
                $queryBuilder->andWhere("$prefix.$key LIKE :$key");
                $queryBuilder->setParameter($key, "%$value%");
            }
            $queryBuilder->setMaxResults(self::SEARCH_LIMIT);
            $list = $queryBuilder->getQuery()->getResult();
        }

        $result = new \Engine\Book\Result\BookList([]);
        $translate = \System\Registry::translation();
        /* @var $o \Data\Doctrine\Main\Book */
        foreach ($list as $o) {
            $book = new \Engine\Book\Result\Book();
            $cnt = $o->getContent();
            $book->setIsbn($o->getId())
                ->setAuthor($o->getAuthor())
                ->setDate($translate->entity(['date', $cnt->getPattern()], $cnt->getLanguage())->getContent())
                ->setDescription($translate->entity(['description', $cnt->getPattern()], $cnt->getLanguage())->getContent())
                ->setImage($translate->entity(['og:image', $cnt->getPattern()], $cnt->getLanguage())->getContent())
                ->setPageCount($o->getPages())
                ->setTitle($o->getTitle());
            $result[$o->getId()] = $book;
        }

        return $list ? $result : array();
    }

    public function createBook($isbn, $language = null)
    {
        $em = $this->getEntityManager();
        $em->beginTransaction();

        if (!$language) {
            $language = \System\Registry::translation()->getTargetLanguage();
        }

        $id = 0;
        $entity = new \Data\Doctrine\Main\Content();
        $entity->setLanguage($language)
            ->setPattern('book/overview/i')
        //    ->setAuthor(\System\Registry::user()->getEntity())
            ->setAccess(Access::EDIT . Access::TRANSLATE . Access::READ)
            ->setUpdatedAt(new \DateTime);

        $data = (new \Modules\Person\Work\Book\Model)->findNewBook($isbn, ['language' => $language]);
        if (!$data['isbn']) {
            throw new \Error\Validation(\System\Registry::translation()->sys('LB_HEADER_404'));
        }

        foreach ($data as $type => $content) {
            $entity->setType($type)
                ->setContent($content)
                ->setSearch($content);
            $o = clone $entity;
            $em->persist($o);
            if (!$id) {
                $em->flush();
                $id = $o->getId();
                $o->setPattern('book/overview/i' . $id);
                $em->persist($o);
                // Update pattern for others
                $entity->setPattern('book/overview/i' . $id);
            }
            if ($o->getType() === 'og:image' || $o->getType() === 'image') {
                try {
                    $img = new \Data\File\Image($content);
                    if ($img->isBlob()) {
                        $fileId = (new \Data\ContentHelper)->saveBlob($o->getPattern(), $o->getType(), $img->getContent());
                        $o->setContent((new \Modules\Files\Model)->saveFile(
                            $fileId,
                            \System\Registry::user()->getEntity()->getUsername(),
                            $id
                        ));
                        $em->persist($o);
                    }
                } catch (\Exception $e) {
                    // avoid error
                }
            }
        }

        $conv = new \System\Converter\StringUtf();

        $book = new \Data\Doctrine\Main\Book();
        $book->setIsbn($data['isbn'])
            ->setYear((new \DateTime($data['date']))->format('Y'))
            ->setAuthor($conv->substr($data['author'], 0, 255))
            ->setTitle($conv->substr($data['og:title'], 0, 255))
            ->setContent($em->getReference(CrMain::CONTENT, $id))
            ->setPages((int) $data['pageCount']);
        $em->persist($book);

        $em->flush();
        $em->commit();
        return $book;

    }
}
