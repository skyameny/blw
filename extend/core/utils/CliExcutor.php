<?php

namespace moudles\core\utils;

use think\Log;
/**
 *
 * 命令行执行类
 * @author cxl
 *
 */
class CliExcutor {

	public $cmdPermission = "";
	public $cmdPath;
	public $cmdName;
	public $cmdParam = array();

	//construct instance
	function __construct($path="") {
		$this->cmdPath = $path;
	}

	public function getCmdLine(){
		$cmdline = $this->cmdPath.$this->cmdName;
		if($this->cmdParam){
			foreach ($this->cmdParam as $param){
				$cmdline.=" ".$param;
			}
		}		
		return $cmdline;
	}

	public function exucteCmdLine(){
		$cmdLine = $this->getCmdLine();
		$result = $this->do_system_command($cmdLine);
		//$printOut = print_r($result["result"], true);
		Log::write("cmd line:".$cmdLine, Log::DEBUG);
		Log::write("cmd returnValue:".$result["returnValue"], Log::DEBUG);
		//Log::write("cmd result:".$printOut, Log::DEBUG);
		return $result;
	}

	protected function do_system_command($cmdline, $needEscape = true){
		if ($needEscape) {
			$cmdline = $this->encodeShellCmd($cmdline);
		}
		$output = array();
		exec($cmdline, $output, $retvalue);
		return array('result'=>$output,'returnValue'=>$retvalue);
	}

	/**
	 * Encode cli command.
	 *
	 * @param string $cmd
	 * @return string
	 */
	protected function encodeShellCmd($cmd) {
		$cmd = trim($cmd)." ";
		$spacePos = -1;
		$firstQuotePos = -1;
		$secondQuotePos = strlen($cmd);
		$retCmd = "";
		$cmdArray = str_split($cmd);
		for ($i=0; $i<count($cmdArray); $i++) {
			if ($cmdArray[$i] == " ") {
				if ($spacePos == -1) {
						
					// first space
					$retCmd = substr($cmd, 0, $i)." ";
					$spacePos = $i;
				} else {
					if ($i > $secondQuotePos) {
						// quoted string
						$retCmd = $retCmd."\"".$this->convert_str_for_cli(substr($cmd, $firstQuotePos+1, $secondQuotePos - $firstQuotePos -1))."\" ";
						$firstQuotePos = -1;
						$secondQuotePos = strlen($cmd);
						$spacePos = $i;
					} else if ($i > $spacePos && $firstQuotePos == -1){
						// no quoted string
						$retCmd = $retCmd.$this->convert_str_for_cli(substr($cmd, $spacePos+1, $i - $spacePos -1))." ";
						$spacePos = $i;
					}
				}
			} else if ($cmdArray[$i] == "\""){
				if ($cmdArray[$i-1] != "\\" || ($cmdArray[$i-1]=="\\" && $cmdArray[$i-2]=="\\")) {
					if ($i > $spacePos) {
						if ($firstQuotePos == -1) {
							$firstQuotePos = $i;
						} else {
							$secondQuotePos = $i;
						}
					}
				}
			}
		}
		return $retCmd;
	}

	//convert str for cli
	protected function convert_str_for_cli($str)
	{
		//convert charactor (\'\"\\) to ('"\) ;
		$retStr = stripslashes($str);
		//convert charactor ('"\) to (\'\"\\);
		$retStr = addslashes($retStr);
		$retStr = str_replace("\\'","'",$retStr);
		$retStr = str_replace('`','\`',$retStr);
		$retStr = str_replace('$','\$',$retStr);
		return $retStr;
	}

	public function excuteCmd($cmd,$param=array()){
		$this->cmdName = $cmd;
		$this->cmdParam = $param;
		$result = $this->exucteCmdLine();
		return $result;
	}

	public function excuteCmdResult($cmd,$param=array()){
		$this->cmdName = $cmd;
		$this->cmdParam = $param;
		$result = $this->exucteCmdLine();
		return $result["result"];
	}

	public function excuteCmdValue($cmd,$param=array()){
		$this->cmdName = $cmd;
		$this->cmdParam = $param;
		$result = $this->exucteCmdLine();
		return $result["returnValue"];
	}
	
	/**
	 * 后台执行命令行
	 * @param 命令行 $cmdLine
	 */
	public static function backExcuteCmd($cmdLine){
		//ExLog::log("后台执行命令行enter:".$cmdLine,log::WARN);
		$cmdLine .= " >/dev/null 2>/dev/null &";
		system($cmdLine,$retVal);
		ExLog::log("后台执行命令行:".$cmdLine, log::DEBUG);
		//return $retVal;
	}
}