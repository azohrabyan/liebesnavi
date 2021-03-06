<?php
/**
 * @title          Chatter Model
 *
 */

namespace PH7;

use PDO;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Model;
use PH7\Framework\Date\CDateTime;

class ChatterModel extends Model
{
    /** @var string */
    protected $sCurrentDate;

    public function __construct()
    {
        parent::__construct();
        $this->sCurrentDate = (new CDateTime)->get()->dateTime('Y-m-d H:i:s');
    }

    public function setLastActivity($iProfileId)
    {
        $this->orm->update('Chatter', 'lastActivity', $this->sCurrentDate, 'profileId', $iProfileId);
    }

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

    public function removeChat($chatterId, $fakeUser, $chatPartner)
    {
        $sSqlQuery = 'DELETE FROM' . Db::prefix('ChatterChats') .
            'WHERE  chatter_id = :chatterId AND fake_user = :fakeUser AND chat_partner = :chatPartner ';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindValue(':chatterId', $chatterId, PDO::PARAM_INT);
        $rStmt->bindValue(':fakeUser', $fakeUser, PDO::PARAM_STR);
        $rStmt->bindValue(':chatPartner', $chatPartner, PDO::PARAM_STR);

        return $rStmt->execute();
    }

    public function insertNote($chatterId, $fakeUser, $chatPartner, $notes, $sDate)
    {
        $sSqlQuery = 'INSERT INTO' . Db::prefix('ChatterNotes') .
            '(chatter_id, fake_user, chat_partner, notes, created) VALUES (:chatterId, :fakeUser, :chatPartner, :notes, :created)';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindValue(':chatterId', $chatterId, PDO::PARAM_INT);
        $rStmt->bindValue(':fakeUser', $fakeUser, PDO::PARAM_STR);
        $rStmt->bindValue(':chatPartner', $chatPartner, PDO::PARAM_STR);
        $rStmt->bindValue(':notes', $notes, PDO::PARAM_STR);
        $rStmt->bindValue(':created', $sDate, PDO::PARAM_STR);

        return $rStmt->execute();
    }

    public function getNotes($fake, $partner)
    {
        $sSqlQuery = 'SELECT * FROM' . Db::prefix('ChatterNotes') . ' WHERE fake_user = :fakeUser AND chat_partner = :chatPartner ORDER BY chatter_note_id DESC LIMIT 1 ';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindValue(':fakeUser', $fake, PDO::PARAM_STR);
        $rStmt->bindValue(':chatPartner', $partner, PDO::PARAM_STR);
        $rStmt->execute();

        $rows = $rStmt->fetchAll(PDO::FETCH_OBJ);
        if (!empty($rows)) {
            $n = $rows[0]->notes;
            return $n;
        }
        return '';
    }

    /**
     * Removes old ChatterChats, i.e.relation between chatter and fake user
     */
    public function cleanOldChats()
    {
        $sSqlQuery = 'DELETE FROM' . Db::prefix('ChatterChats') .
            'WHERE  chatter_id IN (SELECT profileId FROM '. Db::prefix('Chatter') .' WHERE lastActivity IS NULL OR lastActivity < DATE_SUB(\'' . $this->sCurrentDate . '\', INTERVAL 20 MINUTE) ) ';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        return $rStmt->execute();
    }
}
