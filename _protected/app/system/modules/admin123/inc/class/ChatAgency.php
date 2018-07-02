<?php

namespace PH7;


class ChatAgency extends AgencyCore
{

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

        (new AgencyModel)->delete($iProfileId, $sUsername);
    }
}
