<?php

namespace anx1ety\auth;

use anx1ety\auth\Auth;
use anx1ety\auth\Listener;

use pocketmine\plugin\PluginBase;

use pocketmine\Server;

use pocketmine\utils\Config;

class Loader extends PluginBase {
    
    /** @var $instance Loader **/
    public static $instance;
    
    /** @var $formapi Plugin **/
    public $formapi;
    
    /** @var $messages Config **/
    public $messages;
    
    public function onEnable() : void 
    {
        self::$instance = $this;
        
        $this->formapi = Server::getInstance()->getPluginManager()->getPlugin("FormAPI");
        
        if ($this->formapi === null){
            
            $this->getLogger()->info("El plugin AdvancedAuth requiere de la dependencia de FormAPI, porfavor instalela antes de usar el plugin.");
            
            return;
            
        }
        
        @mkdir($this->getDataFolder() . "/players");
        
        $this->saveResource("messages.yml");
        
        $this->messages = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
        
        $this->getServer()->getPluginManager()->registerEvents(new Listener(), $this);
        
        $this->getLogger()->info("El plugin inicio correctamente.");
    }
    
    public function getAuth() : Auth 
    {
        return new Auth($this->formapi);
    }
    
    public static function getInstance() 
    {
        return self::$instance;
    }
}
