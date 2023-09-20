<?php

namespace PerryRylance\Ssh;

class Client extends \Spatie\Ssh\Ssh
{
	public function getExecuteCommand($command): string
    {
		if(PHP_OS_FAMILY != "Windows")
			return Parent::getExecuteCommand($command);

        $commands = $this->wrapArray($command);

        $extraOptions = implode(' ', $this->getExtraOptions());

        $commandString = implode(PHP_EOL, $commands);

        $target = $this->getTargetForSsh();

        if (in_array($this->host, ['local', 'localhost', '127.0.0.1'])) {
            return $commandString;
        }

		return "ssh {$extraOptions} {$target} \"$commandString\"";
    }
}