<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Inc / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Security\Security;

class AgencyModel extends AgencyCoreModel
{
    /**
     * Adding an Admin.
     *
     * @param array $aData
     *
     * @return int The ID of the Admin.
     */
    public function add(array $aData)
    {
        $rStmt = Db::getInstance()->prepare('INSERT INTO' . Db::prefix('ChatAgency') .
            '(agency_name, email, username, password)
        VALUES (:agency_name, :email, :username, :password)');
        $rStmt->bindValue(':agency_name', $aData['agency_name'], \PDO::PARAM_STR);
        $rStmt->bindValue(':email', $aData['email'], \PDO::PARAM_STR);
        $rStmt->bindValue(':username', $aData['username'], \PDO::PARAM_STR);
        $rStmt->bindValue(':password', Security::hashPwd($aData['password']), \PDO::PARAM_STR);
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
        $oDb->exec('DELETE FROM' . Db::prefix('ChatAgency') . 'WHERE profileId = ' . $iProfileId . ' LIMIT 1');
        unset($oDb);
    }

    /**
     * @param int|string $mLooking
     * @param bool $bCount
     * @param string $sOrderBy
     * @param string $iSort
     * @param int $iOffset
     * @param int $iLimit
     *
     * @return int|array
     */
    public function searchAgency($mLooking, $bCount, $sOrderBy, $iSort, $iOffset, $iLimit)
    {
        $bCount = (bool)$bCount;
        $iOffset = (int)$iOffset;
        $iLimit = (int)$iLimit;
        $mLooking = trim($mLooking);

        $sSqlLimit = (!$bCount) ? ' LIMIT :offset, :limit' : '';
        $sSqlSelect = (!$bCount) ? '*' : 'COUNT(profileId) AS totalUsers';

        if (ctype_digit($mLooking)) {
            $sSqlWhere = ' WHERE profileId = :looking';
        } else {
            $sSqlWhere = ' WHERE username LIKE :looking OR agency_name LIKE :looking OR email LIKE :looking ';
        }

        $sSqlOrder = SearchCoreModel::order($sOrderBy, $iSort);

        $rStmt = Db::getInstance()->prepare('SELECT ' . $sSqlSelect . ' FROM' . Db::prefix('ChatAgency') . $sSqlWhere . $sSqlOrder . $sSqlLimit);

        (ctype_digit($mLooking)) ? $rStmt->bindValue(':looking', $mLooking, \PDO::PARAM_INT) : $rStmt->bindValue(':looking', '%' . $mLooking . '%', \PDO::PARAM_STR);

        if (!$bCount) {
            $rStmt->bindParam(':offset', $iOffset, \PDO::PARAM_INT);
            $rStmt->bindParam(':limit', $iLimit, \PDO::PARAM_INT);
        }

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
}
