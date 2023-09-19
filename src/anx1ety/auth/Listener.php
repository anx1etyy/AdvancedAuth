<?php

namespace anx1ety\auth;

use anx1ety\auth\Loader;

use pocketmine\event\Listener as L;

use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;

class Listener implements L {
    
    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        
        if (!Loader::getInstance()->getAuth()->isRegistered($player)){
            
            Loader::getInstance()->getAuth()->openUI($player);
            return;
            
        }
        
        if (Loader::getInstance()->getAuth()->getLoginByIP($player)){
            
            if (strtolower($player->getNetworkSession()->getIp()) === Loader::getInstance()->getAuth()->getLastIP()){
                
                Loader::getInstance()->getAuth()->setAuthenticated($player);
                $player->sendMessage(Loader::getInstance()->messages->get("successfully-logged-by-ip"));
                return;
                
            }
            
            Loader::getInstance()->getAuth()->openUI($player);
            return;
            
        }
        
        Loader::getInstance()->getAuth()->openUI($player);
    }
    
    public function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        
        if (Loader::getInstance()->getAuth()->isRegistered($player)) Loader::getInstance()->getAuth()->setAuthenticated($player, false);
    }
    
    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        
        if (!Loader::getInstance()->getAuth()->isAuthenticated($player)) $event->cancel();
    }
    
    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        
        if (!Loader::getInstance()->getAuth()->isAuthenticated($player)) $event->cancel();
    }
    
    public function onChat(PlayerChatEvent $event)
    {
        $player = $event->getPlayer();
        
        if (!Loader::getInstance()->getAuth()->isAuthenticated($player)) $event->cancel();
    }
    
    public function onCommandPreprocess(PlayerCommandPreprocessEvent $event)
    {
        $player = $event->getPlayer();
        
        if (!Loader::getInstance()->getAuth()->isAuthenticated($player)) $event->cancel();
    }
    
}

