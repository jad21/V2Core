<?php
namespace V2\Core\Commands;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Application as ApplicationBase;

use V2\Core\Logs\Logger;

class Console extends ApplicationBase
{
	protected static $intance = null;

	public static function build()
	{
		if (is_null(self::$intance)) {
			self::init();
		}
		return self::$intance;
	}
	private static function init()
	{
		self::$intance = new self("App Console \n <comment>author: Jose Angel Delgado <info><esojangel@gmail.com><info> </comment>\n", "0.1");
		// ... register commands
		$list_commands = self::$intance->getClassCommands();
		foreach ($list_commands as $class) {
			if (class_exists($class)) {
				self::$intance->add(new $class);
			}
		}
	}
	public function getClassCommands()
	{
		$list_commands = [];
		$commands = env("commands");
		foreach ($commands->list->class as $class) {
			$list_commands[] = (string)$class;
		}
		return $list_commands;
	}
	/**
	 * 	 Correr la aplicacion.
	 *	@author Jose Angel Delgado <esojangel@gmail.com>
	 */
	public static function make()
	{
		try {
			self::build()->run();
		} catch (Exception $e) {
			ErrorHandlerFaltal($e);
		}	
	}

	/**
     * Gets the default input definition.
     *
     * @return InputDefinition An InputDefinition instance
     */
    protected function getDefaultInputDefinition()
    {
        return new InputDefinition(array(
            new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),
            new InputOption('--help', '-h', InputOption::VALUE_NONE, 'Ver las ayudas'),
            new InputOption('--verbose', '-v|vv|vvv', InputOption::VALUE_NONE, 'Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug'),
        ));
    }
    public function callCommandBackground($name,$args=[])
    {
    	$command = $this->find($name);
        if (!$command) {
            throw new Exception("Command {$name} not found", 1);
        }

        if(!is_array($args)){
            $args = [];
        }
        $args["command"] = $name;
        $arrayInput = new ArrayInput($args);
        $command = (string)$arrayInput;
        $base = SERVER_ROOT;
        $cmd = "php {$base}app/bin/console $command";
        if (substr(php_uname(), 0, 7) == "Windows"){ 
            pclose(popen("start /B ". $cmd, "r"));  
        } 
        else { 
            exec($cmd . " > /dev/null &");   
        }
    }
}