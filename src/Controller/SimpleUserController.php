<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SimpleUserController extends AbstractController
{
    private string $CACHE_FILES_DIR_FROM_PUBLIC = '../templates/cache_dir';
    private string $CACHE_FILES_DIR_FROM_TEMPLATES = 'cache_dir';
    private ArticleRepository $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    #[Route('/articles', name: 'liste_articles')]
    public function liste_articles(Request $request): Response
    {

        $page = (!$request->query->get('page')) ? 1 : $request->query->get('page');

        $data = $this->articleRepository->findAllWithPagination($page);

        return $this->render('simple_user/articles.html.twig', $data);
    }
    #regex diso : ^([_-a-z0-9]*)$; regex marina : ^([-_a-z0-9]*)$
    #[Route('{category}/{url}_{id}.html', name: 'fiche_article', requirements:['category'=>'^([-_a-z0-9]*)$' , 'url' => '^([-a-z0-9]*)$' , 'id' => '^([0-9]*)$'])]
    public function ficheArticle(Request $request, string $url, int $id): Response
    {
        $article = $this->articleRepository->findOneByIdAndUrl($id, $url);
        
        if(!$article)throw new Exception(sprintf('findOneByIdAndUrl returned null [id: %s, url:%s]', $id, $url));
        #config avec cache
        /*
        $fileName = $this->creerFichierSiInexistant($article, $id);
        
        return $this->render($fileName);
        */
        #sans cache client
        return $this->render('simple_user/ficheArticle.html.twig', ['article' => $article]);
    }

    function creerFichierSiInexistant(Article $article,string|int $article_id)
    {
        $file_name = $this->CACHE_FILES_DIR_FROM_PUBLIC.'/'.$article_id.'.php';
        if (!file_exists($file_name)) {
            $new_file = fopen($file_name, "w");

            $new_file_content = $this->render('simple_user/ficheArticle.html.twig', ['article' => $article])->getContent();
            
            file_put_contents($file_name, $new_file_content);
            fclose($new_file);
        }
        return $this->CACHE_FILES_DIR_FROM_TEMPLATES.'/'.$article_id.'.php';
    }


    // function creerFichierSiInexistant(string|int $article_id,string $article_url)
    // {
    //     $file_name = $this->CACHE_FILES_DIR.'/'.$article_id.'.php';
    //     if (!file_exists($file_name)) {
    //         $new_file = fopen($file_name, "w");

    //         $article = $this->articleRepository->findOneByIdAndUrl($article_id, $article_url);

    //         $new_file_content = $this->render('ficheArticle.html.twig', ['article' => $article]);

    //         file_put_contents($file_name, $new_file_content);
    //         fclose($new_file);
    //     }
    //     return $file_name;
    // }
}
