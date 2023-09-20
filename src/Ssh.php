<?php

namespace PerryRylance\Ssh;

class Client extends \Spatie\Ssh\Ssh
{
	public function getExecuteCommand($command): string
    {
		if(PHP_OS_FAMILY != "Windows")
			return Parent::getExecuteCommand($command);

        $commands				= $this->wrapArray($command);
		$extraOptions			= $this->getExtraOptions();
        $implodedExtraOptions	= implode(' ', $extraOptions);
        $commandString			= implode(PHP_EOL, $commands);

        $target					= $this->getTargetForSsh();

        if (in_array($this->host, ['local', 'localhost', '127.0.0.1']))
            return $commandString;
		
		// NB: Fix "key too open"
		foreach($extraOptions as $option)
		{
			if(!preg_match('/-i (.+)/', $option, $m))
				continue;
			
			$keyfile = $m[1];

			shell_exec("icacls $keyfile /inheritance:r");
			shell_exec("icacls $keyfile /grant:r \"%username%\":\"(R)\"");
		}

		return "ssh {$implodedExtraOptions} {$target} \"$commandString\"";
    }
}