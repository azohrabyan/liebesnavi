<?php
/**
 * @title          Chatter Model
 *
 */

namespace PH7;

use PDO;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Model;

class ChatterModel extends Model
{
    /**
     * Select chatters chats.
     *
     * @return array SQL content
     */
    public function getAllChattersChats()
    {
        $sSqlQuery = 'SELECT * FROM' . Db::prefix('ChatterChats');

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->execute();

        return $rStmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Add chat between fake user and normal user for chatter.
     *
     * @param int $chatterId ChatterId
     * @param string $fakeUser Username
     * @param string $chatPartner Username 2
     * @param string $sDate In date format: 0000-00-00 00:00:00
     *
     * @return bool Returns TRUE on success or FALSE on failure
     */
    public function createChat($chatterId, $fakeUser, $chatPartner, $sDate)
    {
        $sSqlQuery = 'INSERT INTO' . Db::prefix('ChatterChats') .
            '(chatter_id, fake_user, chat_partner, created) VALUES (:chatterId, :fakeUser, :chatPartner, :created)';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindValue(':chatterId', $chatterId, PDO::PARAM_INT);
        $rStmt->bindValue(':fakeUser', $fakeUser, PDO::PARAM_STR);
        $rStmt->bindValue(':chatPartner', $chatPartner, PDO::PARAM_STR);
        $rStmt->bindValue(':created', $sDate, PDO::PARAM_STR);

        return $rStmt->execute();
    }
}
