<?php 

namespace app\admin\validate;

use think\Validate;

class BuildingValidate extends BlAdminValidate
{
    
    
    
    
    protected  $scene = array(
        "addfamily"=>["family_name","building_id","family_level"],
        "editfamily"=>["family_name","building_id","family_id"],
    );
    
    
    
}

?>