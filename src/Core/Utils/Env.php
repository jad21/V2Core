<?php
namespace V2\Core\Utils;
use Symfony\Component\Yaml\Yaml;
use Exception;

class Env
{
    private $configfile              = CONFIG_DIRECTORY;
    public static $setting           = [];
    public static $extension_default = "xml";

    private $is_xml = true;

    public function __construct($file = null)
    {
        if (is_null($file)) {
            $this->configfile = CONFIG_DIRECTORY . CONFIG_FILE;
        } else {
            $this->configfile = CONFIG_DIRECTORY . $file;
        }
        self::$setting[$file] = $this->getDataFile();
    }
    public static function getData($file = null)
    {
        if (!isset(self::$setting[$file]) and empty(self::$setting[$file])) {
            new self($file);
        }
        return self::$setting[$file];
    }
    public function getDataFile()
    {
        $path_extension = strtolower(pathinfo($this->configfile, PATHINFO_EXTENSION));
        if (empty($path_extension) or $path_extension == "") {
            $path_extension = self::$extension_default;
            $this->configfile .= "." . $path_extension;
        }
        if (file_exists($this->configfile)) {
            switch ($path_extension) {
                case 'xml':
                    $data = simplexml_load_file($this->configfile);
                    break;
                case 'json':
                    $data = json_decode(file_get_contents($this->configfile));
                    break;
                case 'yaml':
                	if (class_exists(\Symfony\Component\Yaml\Yaml::class)) {
                		$data = Yaml::parse(file_get_contents($this->configfile),\Symfony\Component\Yaml\Yaml::PARSE_OBJECT);
                	}else{
                    	$data = yaml_parse_file($this->configfile);
                	}
                    if (not(is_object($data))) {
                    	$data = json_decode(json_encode($data), false);
                    }
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
