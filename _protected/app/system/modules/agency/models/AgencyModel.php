<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Inc / Model
 */

namespace PH7;

use PH7\Framework\Date\CDateTime;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Security\Security;

class AgencyModel extends AgencyCoreModel
{
    /**
     * It recreates an admin method more complicated and more secure than the classic one PH7\UserCoreModel::login()
     *
     * @param string $sEmail
     * @param string $sUsername
     * @param string $sPassword
     *
     * @return bool Returns TRUE if successful otherwise FALSE
     */
    public function agencyLogin($sEmail, $sUsername, $sPassword)
    {
        $rStmt = Db::getInstance()->prepare('SELECT password FROM' .
            Db::prefix('ChatAgency') . 'WHERE email = :email AND username = :username LIMIT 1');
        $rStmt->bindValue(':email', $sEmail, \PDO::PARAM_STR);
        $rStmt->bindValue(':username', $sUsername, \PDO::PARAM_STR);
        $rStmt->execute();
        $oRow = $rStmt->fetch(\PDO::FETCH_OBJ);
        Db::free($rStmt);

        return Security::checkPwd($sPassword, @$oRow->password);
    }

}
