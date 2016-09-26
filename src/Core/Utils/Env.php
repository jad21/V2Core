<?php 
namespace V2\Core\Utils;
use Exception;

class Env
{
	private $configfile = CONFIG_DIRECTORY;
	public static $setting = [];
	private $is_xml = true;
	public function __construct($file = null)
	{
		if (is_null($file)) {
			$this->configfile = CONFIG_DIRECTORY . CONFIG_FILE;
		}else{
			$this->configfile = CONFIG_DIRECTORY . $file;
		}
		self::$setting[$file] = $this->getDataFile();
	}
	public static function getData($file=null)
	{
		if (!isset(self::$setting[$file]) AND empty(self::$setting[$file])) {
			new self($file);
		}
		return self::$setting[$file];
	}
	public function getDataFile(){
		$path_extension = strtolower(pathinfo($this->configfile, PATHINFO_EXTENSION));
		if (empty($path_extension) OR $path_extension == "") {
			$path_extension = "xml";
			$this->configfile .= ".".$path_extension;
		}
		if (file_exists($this->configfile)){
            switch ($path_extension) {
            	case 'xml':
            		$data = simplexml_load_file($this->configfile);
            		break;
            	case 'json':
            		$data = json_decode(file_get_contents($this->configfile));
            		break;
            	default:
            		$data = file_get_contents($this->configfile);
            		break;
            }
            return $data;
        } else {
            throw new Exception("File no found {$this->configfile}");
        }
	}
	
}