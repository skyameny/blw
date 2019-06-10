<?php
namespace authority\includes\identify;

use core\includes\session\SessionManagement;

class SessionIdentify implements Identify
{
    public function getIdentifyUser()
    {
        SessionManagement::getSession()->getUser();
    }

    public function refresh()
    {
        // TODO: Implement refresh() method.
    }
}
