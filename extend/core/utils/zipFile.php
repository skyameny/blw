<?php
namespace moudles\core\utils;


class zipFile{ // class start

    /**
     * 
     * @param string $tgzFilePath
     * @param string $path
     * @return unknown|array
     */
    public static function unZipFile($tgzFilePath = '', $path = ''){
        //解压
        $cmdLine = sprintf("tar -zxvf ".$tgzFilePath ."  -C  ".$path );
        $comand = new CliExcutor();
        ExLog::log("执行解压.tgz压缩文件命令:" . $cmdLine);
        $result = $comand->excuteCmd($cmdLine);
        ExLog::log($result,ExLog::DEBUG);
        return $result["returnValue"];
    }


    public static function delSurPlusFile($xmlFilePath = '', $customerFilePath = '', $tgzFilePath = ''){
        $comand = new CliExcutor();
        $xmlCmdLine = sprintf(" rm  -f  ".$xmlFilePath);
        $comand->excuteCmd($xmlCmdLine);
        
        $customerCmdLine = sprintf(" rm  -f  ".$customerFilePath);
        $comand->excuteCmd($customerCmdLine);
        
        $cmdLine = sprintf(" rm  -f  ".$tgzFilePath);
        $comand->excuteCmd($cmdLine);
        return true;
    }

} // class end