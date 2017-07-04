<?php

namespace src\Web\Command;

use Group\Sync\Console\Command as Command;

class LogClearCommand extends Command
{	
	//实现init方法就行
	public function init()
	{	
		//获取终端输入的参数
		//$this->getArgv();
		echo "清除\n";
	}
}