<?php

namespace anx1ety\auth;

use anx1ety\auth\Loader;

use pocketmine\player\Player;

use pocketmine\Server;

use pocketmine\utils\Config;

final class Auth {
    
    /** @var $formapi Plugin **/
    private $formapi;
    
    public function __construct($formapi)
    {
        $this->formapi = $formapi;
    }
    
    public function openUI(Player $player)
    {
        $form = $this->formapi->createCustomForm(function(Player $player, $result){
            
            if ($result === null){
                $this->openUI($player);
                return;
            }
            
            if ($this->isRegistered($player)){
                
                if ($result[1] !== $this->getPassword($player)){
                    $player->kick(Loader::getInstance()->messages->get("kick-wrong-password"));
                    return;
                }
                
                $this->setAuthenticated($player);
                $player->sendMessage(Loader::getInstance()->messages->get("successfully-logged"));
               
                if ($result[2] === true){
                    $this->setLoginByIP($player);
                }
                
            } else {
                
                $this->setAuthenticated($player, true, $result[1]);
                $player->sendMessage(Loader::getInstance()->messages->get("successfully-registered"));
                
                if ($result[2] === true){
                    $this->setLoginByIP($player);
                }
                
            }
        });
        
        if (!$this->isRegistered($player)){
            
            $form->setTitle(Loader::getInstance()->messages->get("register-title-form"));
            $form->addLabel(Loader::getInstance()->messages->get("register-label-form"));
            $form->addInput(Loader::getInstance()->messages->get("register-input-form")[0], Loader::getInstance()->messages->get("register-input-form")[1]);
            $form->addToggle(Loader::getInstance()->messages->get("auth-by-ip-form"), false);
            
        } else {
            
            $form->setTitle(Loader::getInstance()->messages->get("login-title-form"));
            $form->addLabel(Loader::getInstance()->messages->get("login-label-form"));
            $form->addInput(Loader::getInstance()->messages->get("register-input-form")[0], Loader::getInstance()->messages->get("register-input-form")[1]);
            $form->addToggle(Loader::getInstance()->messages->get("auth-by-ip-form"), false);
            
        }
        
        $form->sendToPlayer($player);
    }
    
    public function isRegistered(Player $player) : bool 
    {
        return file_exists(Loader::getInstance()->getDataFolder() . "/players/" . strtolower($player->getName()) . ".yml");
    }
    
    public function isAuthenticated(Player $player) : bool 
    {
        if (!$this->isRegistered($player)) return false;
        
        $database = $this->getDataBase($player);
        
        return $database->get("authenticated");
    }
    
    public function getPassword(Player $player) : ?string
    {
        if (!$this->isRegistered($player)) return null;
        
        $database = $this->getDataBase($player);
        
        return $database->get("password");
    }
    
    public function getLastIP(Player $player) : ?string
    {
        if (!$this->isRegistered($player)) return null;
        
        $database = $this->getDataBase($player);
        
        return $database->get("last-ip");
    }
    
    public function getLoginByIP(Player $player) : bool
    {
        if (!$this->isRegistered($player)) return false;
        
        $database = $this->getDataBase($player);
        
        return $database->get("login-by-ip");
    }
    
    public function getDataBase(Player $player) : Config
    {
        return $database = new Config(Loader::getInstance()->getDataFolder() . "/players/" . strtolower($player->getName()) . ".yml", Config::YAML);
    }
    
    public function setAuthenticated(Player $player, bool $bool = true, $password = null)
    {
        $database = $this->getDataBase($player);
        
        if ($bool === true){
            
            if ($password !== null){
                
                $database->set("password", $password);
                $database->set("last-ip", strtolower($player->getNetworkSession()->getIp()));
                $database->set("authenticated", true);
                $database->save();
                
            } else {
                
                $database->set("password", $this->getPassword($player));
                $database->set("last-ip", strtolower($player->getNetworkSession()->getIp()));
                $database->set("authenticated", true);
                $database->save();
                
            }
            
        } else {
            
            $database->set("password", $this->getPassword($player));
            $database->set("last-ip", $this->getLastIP($player));
            $database->set("authenticated", false);
            $database->save();
            
        }
    }
    
    public function setLoginByIP(Player $player, bool $bool = true)
    {
        $database = $this->getDataBase($player);
        
        $database->set("login-by-ip", $bool);
        $database->save();
    }
}
