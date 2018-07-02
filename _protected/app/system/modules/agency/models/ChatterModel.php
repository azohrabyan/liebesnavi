<?php

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Security\Security;

class ChatterModel extends ChatterCoreModel
{
    /**
     * Adding an Chatter.
     *
     * @param array $aData
     *
     * @return int The ID of the Chatter.
     */
    public function add(array $aData)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('Chatter') .
            '(name, email, username, password, agency_id)
        VALUES (:chatter_name, :email, :username, :password, :agencyId)');
        $rStmt->bindValue(':chatter_name', $aData['chatter_name'], \PDO::PARAM_STR);
        $rStmt->bindValue(':email', $aData['email'], \PDO::PARAM_STR);
        $rStmt->bindValue(':username', $aData['username'], \PDO::PARAM_STR);
        $rStmt->bindValue(':password', Security::hashPwd($aData['password']), \PDO::PARAM_STR);
        $rStmt->bindValue(':agencyId', $aData['agency_id'], \PDO::PARAM_INT);
        $rStmt->execute();
        Db::free($rStmt);

        return Db::getInstance()->lastInsertId();
    }

    /**
     * Delete Admin.
     *
     * @param int $iProfileId
     * @param string $sUsername
     *
     * @return void
     */
    public function delete($iProfileId, $sUsername)
    {
        $iProfileId = (int)$iProfileId;

        $oDb = Db::getInstance();
        $oDb->exec('DELETE FROM' . Db::prefix('Chatter') . 'WHERE profileId = ' . $iProfileId . ' LIMIT 1');
        unset($oDb);
    }

    /**
     * @param int $agencyId
     * @param bool $bCount
     * @param string $sOrderBy
     * @param string $iSort
     *
     * @return int|array
     */
    public function searchChatters($agencyId, $bCount, $sOrderBy, $iSort)
    {
        $bCount = (bool)$bCount;
        $agencyId = trim($agencyId);

        $sSqlLimit = (!$bCount) ? ' LIMIT :offset, :limit' : '';
        $sSqlSelect = (!$bCount) ? '*' : 'COUNT(profileId) AS totalUsers';

        $sSqlWhere = ' WHERE agency_id = :looking ';

        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort);

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM' . Db::prefix('Chatter') . $sSqlWhere . $sSqlOrder );

        $rStmt->bindValue(':looking', $agencyId, \PDO::PARAM_INT);

        $rStmt->execute();

        if (!$bCount) {
            $mData = $rStmt->fetchAll(\PDO::FETCH_OBJ);
            Db::free($rStmt);
        } else {
            $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
            Db::free($rStmt);
            $mData = (int)$oRow->totalUsers;
            unset($oRow);
        }

        return $mData;
    }

    /**
     * It recreates an admin method more complicated and more secure than the classic one PH7\UserCoreModel::login()
     *
     * @param string $sEmail
     * @param string $sUsername
     * @param string $sPassword
     *
     * @return bool Returns TRUE if successful otherwise FALSE
     */
    public function chatterLogin($sEmail, $sUsername, $sPassword)
    {
        $rStmt = Db::getInstance()->prepare('SELECT password FROM' .
            Db::prefix('Chatter') . 'WHERE email = :email AND username = :username LIMIT 1');
        $rStmt->bindValue(':email', $sEmail, \PDO::PARAM_STR);
        $rStmt->bindValue(':username', $sUsername, \PDO::PARAM_STR);
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);

        return Security::checkPwd($sPassword, @$oRow->password);
    }
}
