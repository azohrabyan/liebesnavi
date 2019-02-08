<?php
/**
 * @title          Messenger Model
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7/ App / System / Module / IM / Model
 */

namespace PH7;

use PDO;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Model\Engine\Model;

class MessengerModel extends Model
{
    /**
     * Select Data of content messenger.
     *
     * @param string $sTo Username
     *
     * @return \stdClass SQL content
     */
    public function select($sTo)
    {
        $sSqlQuery = 'SELECT * FROM' . Db::prefix('Messenger') .
            'WHERE (toUser = :to AND recd = 0) ORDER BY messengerId ASC';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindValue(':to', $sTo, PDO::PARAM_STR);
        $rStmt->execute();

        return $rStmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function selectLatest($sTo)
    {
        $sSqlQuery = 'SELECT * FROM' . Db::prefix('Messenger') .
            'WHERE (toUser = :to) ORDER BY messengerId ASC LIMIT 0, 10';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindValue(':to', $sTo, PDO::PARAM_STR);
        $rStmt->execute();

        return $rStmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function selectFromToRead($sFrom, $sTo)
    {
        $sSqlQuery = 'SELECT * FROM' . Db::prefix('Messenger') .
            'WHERE recd=1 AND ( (fromUser = :from AND toUser = :to) OR (fromUser = :to AND toUser = :from)) ORDER BY messengerId ASC';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindValue(':from', $sFrom, PDO::PARAM_STR);
        $rStmt->bindValue(':to', $sTo, PDO::PARAM_STR);
        $rStmt->execute();

        return $rStmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function selectFromToUnread($sFrom, $sTo)
    {
        $sSqlQuery = 'SELECT * FROM' . Db::prefix('Messenger') .
            'WHERE ((fromUser = :from AND toUser = :to) ) AND recd = 0 ORDER BY messengerId ASC';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindValue(':from', $sFrom, PDO::PARAM_STR);
        $rStmt->bindValue(':to', $sTo, PDO::PARAM_STR);
        $rStmt->execute();

        return $rStmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function selectAllRead($sFrom)
    {
        $sSqlQuery = 'SELECT * FROM' . Db::prefix('Messenger') .
            'WHERE ((fromUser = :from ) OR (toUser = :from))  AND recd = 1 ORDER BY messengerId ASC';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindValue(':from', $sFrom, PDO::PARAM_STR);
        $rStmt->execute();

        return $rStmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function selectUnread($sTo)
    {
        $sSqlQuery = 'SELECT * FROM' . Db::prefix('Messenger') .
            'WHERE (toUser = :to AND recd = 0) ORDER BY messengerId ASC';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindValue(':to', $sTo, PDO::PARAM_STR);
        $rStmt->execute();

        return $rStmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function selectUnreadForFakes()
    {
        $sSqlQuery = 'SELECT msg.* FROM ' . Db::prefix('Messenger') . ' msg
            INNER JOIN ' . Db::prefix('Members') . ' mmb on msg.toUser = mmb.username AND mmb.is_fake
            WHERE recd = 0 ORDER BY messengerId ASC';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->execute();

        return $rStmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function markAsRead($messageIds)
    {
        if (empty($messageIds)) {
            return;
        }
        $inQuery = implode(',', array_fill(0, count($messageIds), '?'));

        $sSqlQuery = 'UPDATE' . Db::prefix('Messenger') .
            'SET recd = 1 WHERE messengerId IN (' . $inQuery . ')';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);

        foreach ($messageIds as $k => $id) {
            $rStmt->bindValue(($k + 1), $id);
        }

        return $rStmt->execute();
    }

    /**
     * Update Message.
     *
     * @param string $sFrom The 'from' username
     * @param string $sTo The 'to' username
     *
     * @return bool Returns TRUE on success or FALSE on failure
     */
    public function update($sFrom, $sTo)
    {
        $sSqlQuery = 'UPDATE' . Db::prefix('Messenger') .
            'SET recd = 1 WHERE (fromUser = :from OR toUser = :to) AND recd = 0';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindValue(':from', $sFrom, PDO::PARAM_STR);
        $rStmt->bindValue(':to', $sTo, PDO::PARAM_STR);

        return $rStmt->execute();
    }

    /**
     * Add a new message.
     *
     * @param string $sFrom Username
     * @param string $sTo Username 2
     * @param string $sMessage Message content
     * @param string $sDate In date format: 0000-00-00 00:00:00
     *
     * @return bool Returns TRUE on success or FALSE on failure
     */
    public function insert($sFrom, $sTo, $sMessage, $sDate, $chatterId = 0)
    {
        $sSqlQuery = 'INSERT INTO' . Db::prefix('Messenger') .
            '(fromUser, toUser, message, sent, chatter_id) VALUES (:from, :to, :message, :date, :chatterId)';

        $rStmt = Db::getInstance()->prepare($sSqlQuery);
        $rStmt->bindValue(':from', $sFrom, PDO::PARAM_STR);
        $rStmt->bindValue(':to', $sTo, PDO::PARAM_STR);
        $rStmt->bindValue(':message', $sMessage, PDO::PARAM_STR);
        $rStmt->bindValue(':date', $sDate, PDO::PARAM_STR);
        $rStmt->bindValue(':chatterId', $chatterId, PDO::PARAM_INT);

        return $rStmt->execute();
    }
}
