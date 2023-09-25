<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleFilterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticlesController extends AbstractController
{

    #[Route(path: "/")]
    public function index(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $sources = $entityManager->getRepository(Article::class)
            ->createQueryBuilder('a')
            ->select('a.source')
            ->distinct()
            ->getQuery()
            ->getSingleColumnResult()
        ;

        $filter = $this->createForm(ArticleFilterFormType::class, null, [
            'sources' => array_combine($sources, $sources),
            'method' => 'GET'
        ]);

        $filter->handleRequest($request);

        $query = $entityManager->getRepository(Article::class)
            ->createQueryBuilder('a')
            ->orderBy('a.publishedAt', "DESC");

        if ($filter->isSubmitted() && $filter->isValid()) {
            $selectedSources = $filter->get('sources')->getData();
            if (!empty($selectedSources)) {
                $query->andWhere('a.source IN (:sources)')
                    ->setParameter('sources', $selectedSources);
            }
        }

        $query = $query->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('articles/index.html.twig', [
            'pagination' => $pagination,
            'filter' => $filter
        ]);
    }
}
