<?php 

namespace app\bladmin\validate;

use think\Validate;

class BuildingValidate extends BlAdminValidate
{
    
    
    
    
    protected  $scene = array(
        "addbuilding"=>["building_name","community_id","building_level"],
        "editbuilding"=>["building_name","community_id","building_level"],
    );
    
    
    
}

?>