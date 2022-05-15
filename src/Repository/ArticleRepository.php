<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    private $PAGE_SIZE = 5;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function add(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Article[] Returns an array of Article objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Article
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


   public function findAllWithPagination(int $page): array
   {
       $query = $this->createQueryBuilder('a')
           ->orderBy('a.id', 'ASC')
           ->getQuery()
       ;
        // load doctrine Paginator
        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query);
        // you can get total items
        $totalItems = count($paginator);
        // get total pages
        $pageCount = ceil($totalItems / $this->PAGE_SIZE);
        if($page>$pageCount){
            throw new Exception(sprintf(' $GET[page] (%s) > total pageCount (%s) in ArticleRepository->findAllWithPagination(%s)',$page, $pageCount,$page));
        }
        // now get one page's items:
        $paginator
            ->getQuery()
            ->setFirstResult($this->PAGE_SIZE * ($page-1)) // set the offset
            ->setMaxResults($this->PAGE_SIZE); // set the limit
        // foreach ($paginator as $pageItem) {
        //     // do stuff with results...
        //     dump($pageItem);
        // }
        // die();
       return [
           'articles' => $paginator,
           'totalItems' => $totalItems,
           'pageCount' => $pageCount
       ];
   }

   public function findOneByIdAndUrl(int $id,string $urlPath): ?Article
   {
       return $this->createQueryBuilder('a')
           ->andWhere('a.url_path = :url_path')
           ->andWhere('a.id = :id')
           ->setParameter('id', $id)
           ->setParameter('url_path', $urlPath)
           ->orderBy('a.id', 'ASC')
           ->getQuery()
          ->getOneOrNullResult()
       ;
   }
}
