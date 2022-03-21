<?php
namespace App\Controller;

use App\View;
use App\Database;
use App\Redirect;
use App\Model\Friend;
use Doctrine\DBAL\Exception;

class FriendsController
{
    /**
     * @throws Exception
     */
    public function show(array $vars): View
    {
        $userFriends = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('friends')
            ->where("user_id = ? and user_id in (select friend_id from friends where user_id)")
            ->setParameter(0, (int) $vars['id'])
            ->fetchAllAssociative();

        // Invite list
        $userInvites = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('friends')
            ->where("friend_id = ?")
            ->setParameter(0, (int) $vars['id'])
            ->fetchAllAssociative();

        $friendsId = [];
        $inviteId = [];

        if (empty($userFriends)) {
            $userFriends = [""];
        }
        foreach ($userFriends as $friend) {
            foreach ($userInvites as $invite) {
                if ($friend['user_id'] == $invite['friend_id'] && $invite['user_id'] == $friend['friend_id']) {
                    $friendsId[] = $friend['friend_id'];
                }
                if ($invite['user_id'] != $friend["friend_id"]) {
                    $inviteId[] = $invite['user_id'];
                }
            }
        }

        $inviteId = array_unique(array_diff($inviteId, $friendsId));
        $friendList = [];
        foreach ($friendsId as $id) {
            $friend = Database::connection()
                ->createQueryBuilder()
                ->select('*')
                ->from('user_profiles')
                ->where("user_id = ?")
                ->setParameter(0, $id)
                ->fetchAllAssociative();
            $friendList[] = new Friend(
                $friend[0]['name'],
                $friend[0]['surname'],
                (int)$friend[0]['user_id']
            );
        }
        $inviteList = [];
        foreach ($inviteId as $id) {
            $invite = Database::connection()
                ->createQueryBuilder()
                ->select('*')
                ->from('user_profiles')
                ->where("user_id = ?")
                ->setParameter(0, $id)
                ->fetchAllAssociative();
            $inviteList[] = new Friend(
                $invite[0]['name'],
                $invite[0]['surname'],
                (int)$invite[0]['user_id']
            );
        }


        $searchFriend = [];
        //var_dump($_SESSION);
        if (isset($_SESSION['searchFriend'])) {
            //var_dump($_SESSION);
            foreach ($_SESSION['searchFriend'] as $search) {
                $search = Database::connection()
                    ->createQueryBuilder()
                    ->select('*')
                    ->from('user_profiles')
                    ->where("user_id = ?")
                    ->setParameter(0, $search)
                    ->fetchAllAssociative();
                $searchFriend[] = new Friend(
                    $search[0]['name'],
                    $search[0]['surname'],
                    $search[0]['user_id']
                );
            }
        }


        return new View("Users/friends", [
            "friends" => $friendList,
            "invites" => $inviteList,
            "searchResults" => $searchFriend
        ]);
    }

    /**
     * @throws Exception
     */
    public function accept(array $vars):Redirect
    {
        Database::connection()->insert("friends", [
            'user_id' => $_SESSION['userid'],
            'friend_id' => (int)$vars['id']
        ]);

        return new Redirect("/friends/{$_SESSION['userid']}");
    }

    /**
     * @throws Exception
     */
    public function search():Redirect
    {
        $searchResults = ucfirst($_POST['search']);


        $search = Database::connection()
            ->createQueryBuilder()
            ->select('*')
            ->from('user_profiles')
            ->where("name = ? and user_id not in (select friend_id from friends where user_id = ?)")
            ->setParameter(0, $searchResults)
            ->setParameter(1, $_SESSION['userid'])
            ->fetchAllAssociative();

        $results = [];
        foreach ($search as $result) {
            $results[] =(int)$result['user_id'];
        }

        $_SESSION['searchFriend'] = $results;

        return new Redirect("/friends/{$_SESSION['userid']}");
    }
}