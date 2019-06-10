<?php
/**
 * 窗口控制器
 */
namespace core\controller;

use authority\includes\user\AnonymousUser;
use authority\service\IdentifyService;
use authority\traits\Authentication;
use core\includes\session\SessionManagement;
use core\exception\CoreException;

abstract class Frame extends Base
{
    use Authentication;

    protected $client_type = CLIENT_WEBSITE;
    protected $no_auth_action = [];
    /**
     * @var IdentifyService
     */
    protected $identifyService ;
    /**
     * 访问企业
     * @see EnterpriseService
     * @var EnterpriseService
     */
    protected $enterpriseService;

    public function _initialize()
    {
        $this->identifyService = IdentifyService::singleton();
        parent::_initialize();
        $this->authorize();
    }

    /**
     * 验证是否能访问
     * @throws CoreException
     */
    protected function authorize()
    {
        $current_action = $this->request->action();
        $identifyUser = $this->identifyService->getIdentifyUser();
        if (!in_array($current_action, $this->no_auth_action)) {

            if ($identifyUser instanceof AnonymousUser) {
                $this->result("", STATUS_CODE_LOGIN_AUTH_FAILED);
            }
            $authRes = $this->verification();
            if($authRes === false){
                $this->result_failed(NO_RIGHT_TO_OPERATE);
            }
        }
    }

}