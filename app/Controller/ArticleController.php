<?php
namespace App\Controller;

use App\View;
use App\Model\Article;
use App\Database;
use App\Redirect;
use App\Exceptions\FormValidationException;
use App\Validation\ArticleFormValidation;
use App\Validation\Errors;
use App\Model\Comment;
use Doctrine\DBAL\Exception;

class ArticleController
{
    /**
     * @throws Exception
     */
    public function index():View
    {
        $articlesQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('articles')
            ->orderBy('created_at', 'desc')
            ->fetchAllAssociative();




        $articles = [];

        foreach ($articlesQuery as $article) {
            $articles[] = new Article(
                $article['author'],
                (int)$article['author_id'],
                $article['title'],
                $article['description_text'],
                $article['created_at'],
                $article['id'],
            );
        }



        return new View("Articles/index", [
            'articles' => $articles,
            'userName' => $_SESSION['name'],
            'userId' => $_SESSION['userid'],
        ]);
    }

    /**
     * @throws Exception
     */
    public function show(array $vars):View
    {
        $articlesQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('articles')
            ->where("id = ?")
            ->setParameter(0, (int) $vars['id'])
            ->fetchAllAssociative();



        $article = new Article(
            $articlesQuery[0]['author'],
            $articlesQuery[0]['author_id'],
            $articlesQuery[0]['title'],
            $articlesQuery[0]['description_text'],
            $articlesQuery[0]['created_at'],
            $articlesQuery[0]['id']
        );

        $commentsQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('article_comments')
            ->where("article_id = ?")
            ->orderBy('commented_at', 'desc')
            ->setParameter(0, (int) $vars['id'])
            ->fetchAllAssociative();

        $comments = [];

        foreach ($commentsQuery as $comment) {
            $commentAuthor = Database::connection()
                ->createQueryBuilder()
                ->select('*')
                ->from('user_profiles')
                ->where("user_id = ?")
                ->setParameter(0, $comment['user_id'])
                ->fetchAllAssociative();

            $commentDate = explode(" ", $comment['commented_at'])[0];

            $comments[] = new Comment(
                $commentAuthor[0]['name'],
                $commentAuthor[0]['surname'],
                $comment['comment'],
                (int)$comment['user_id'],
                (int)$comment['article_id'],
                $commentDate,
                $comment['id']
            );
        }


        $articleLikes = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('article_likes')
            ->where('article_id = ?')
            ->setParameter(0, (int) $vars['id'])
            ->fetchAllAssociative();

        $likeId=[];
        foreach ($articleLikes as $like) {
            $likeId[] = $like['user_id'];
        }


        $likeCount = count($articleLikes);


        return new View("Articles/show", [
            'article' => $article,
            'articleLikes' => $articleLikes,
            'likeCount'=> $likeCount,
            'likeId'=> $likeId,
            'comments' => $comments,
            'userName' => $_SESSION['name'],
            'userId' => $_SESSION['userid']
        ]);
    }
    public function create():View
    {
        return new View('Articles/create', [
            'errors' => Errors::getAll(),
            'inputs' => $_SESSION['inputs'] ?? []
        ]);
    }

    /**
     * @throws Exception
     */
    public function store():Redirect
    {
        try {
            $validator = (new ArticleFormValidation($_POST));
            $validator->passes();
        } catch (FormValidationException $exception) {
            $_SESSION['errors'] = $validator->getErrors();
            $_SESSION['inputs'] = $_POST;
            return new Redirect("/articles/create");
        }

        $articlesQuery = Database::connection()
            ->insert('articles', [
                'title' => $_POST['title'],
                'description_text' => $_POST['description'],
                'author' => $_SESSION['name'],
                'author_id' => $_SESSION['userid'],
            ]);

        return new Redirect('/articles');
    }

    /**
     * @throws Exception
     */
    public function delete(array $vars):Redirect
    {
        $articlesQuery = Database::connection()
            ->delete('articles', ['id'=>(int)$vars['id']
            ]);

        return new Redirect('/articles');
    }

    /**
     * @throws Exception
     */
    public function edit(array $vars):View
    {
        $articlesQuery = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('articles')
            ->where("id = ?")
            ->setParameter(0, (int) $vars['id'])
            ->fetchAllAssociative();


        $article = new Article(
            $articlesQuery[0]['author'],
            $articlesQuery[0]['author_id'],
            $articlesQuery[0]['title'],
            $articlesQuery[0]['description_text'],
            $articlesQuery[0]['created_at'],
            $articlesQuery[0]['id']
        );

        return new View("Articles/edit", [
            'article' => $article
        ]);
    }

    /**
     * @throws Exception
     */
    public function update(array $vars):Redirect
    {
        Database::connection()->update("articles", [
            'title' => $_POST['title'],
            'description_text' => $_POST['description'],
        ], ['id' => (int)$vars['id']]);



        return new Redirect("/articles");
    }

    /**
     * @throws Exception
     */
    public function like(array $vars):Redirect
    {
        $articleId = (int)$vars['id'];
        Database::connection()->insert("article_likes", [
            'article_id' => $articleId,
            'user_id' => $_SESSION['userid']
        ]);

        return new Redirect("/articles/{$articleId}");
    }

    /**
     * @throws Exception
     */
    public function unlike(array $vars):Redirect
    {
        $articleId = (int)$vars['id'];
        Database::connection()->delete('article_likes', [
            'article_id'=> $articleId,
            'user_id' => $_SESSION['userid']
        ]);

        return new Redirect("/articles/{$articleId}");
    }

    /**
     * @throws Exception
     */
    public function comment(array $vars):Redirect
    {
        $articleId = (int)$vars['id'];
        Database::connection()->insert("article_comments", [
            'article_id' => $articleId,
            'user_id' => $_SESSION['userid'],
            'comment' => $_POST['comment']
        ]);
        return new Redirect("/articles/{$articleId}");
    }

    /**
     * @throws Exception
     */
    public function deleteComment(array $vars):Redirect
    {
        $commentId = (int)$vars['id'];
        $articleId = (int)$vars['article_id'];

        Database::connection()
            ->delete('article_comments', ['id'=>$commentId
            ]);

        return new Redirect("/articles/{$articleId}");
    }
}