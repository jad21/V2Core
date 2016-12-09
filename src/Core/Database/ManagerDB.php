<?php
namespace V2\Core\Database;

class ManagerDB
{
    
    protected static $dbconections = [];

    public static function getConnection($name=null)
    {
        if(empty($name)){
            $name = etc()->db->default;
        }
        if (!isset(self::$dbconections[$name])) {
            self::$dbconections[$name] = new DB($name);
        }
        return self::$dbconections[$name];
    }
    public static function closeConnection($name,$force=false)
    {
    	if(isset(self::$dbconections[$name])){
    		self::$dbconections[$name]->ifNotTransactionCloseConnection($force);
    		return true;
    	}
    	return false;
    }
    public static function closeAllConnection($force = false)
    {
    	foreach (self::$dbconections as $name => $db) {

    		self::closeConnection($name,$force);
    	}
    }
}
