<?php
/**
 *  ____  _            _______ _          _____
 * |  _ \| |          |__   __| |        |  __ \
 * | |_) | | __ _ _______| |  | |__   ___| |  | | _____   __
 * |  _ <| |/ _` |_  / _ \ |  | '_ \ / _ \ |  | |/ _ \ \ / /
 * | |_) | | (_| |/ /  __/ |  | | | |  __/ |__| |  __/\ V /
 * |____/|_|\__,_/___\___|_|  |_| |_|\___|_____/ \___| \_/
 *
 * Copyright (C) 2018 iiFlamiinBlaze
 *
 * iiFlamiinBlaze's plugins are licensed under MIT license!
 * Made by iiFlamiinBlaze for the PocketMine-MP Community!
 *
 * @author iiFlamiinBlaze
 * Twitter: https://twitter.com/iiFlamiinBlaze
 * GitHub: https://github.com/iiFlamiinBlaze
 * Discord: https://discord.gg/znEsFsG
 */
declare(strict_types=1);

namespace iiFlamiinBlaze\BlazinFly;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\utils\TextFormat;

class BlazinFly extends PluginBase implements Listener{

    const PREFIX = TextFormat::AQUA . "BlazinFly" . TextFormat::GOLD . " > ";
    const VERSION = "v1.8.3";

    public function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->getLogger()->info("BlazinFly " . self::VERSION . " by iiFlamiinBlaze enabled");
    }

    public function onJoin(PlayerJoinEvent $event) : void{
        $player = $event->getPlayer();
        if($this->getConfig()->get("onJoin_FlyReset") === true){
            if($player->isCreative()) return;
            $player->setAllowFlight(false);
            $player->setFlying(false);
            $player->sendMessage($this->getConfig()->get("fly_disabled"));
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if($command->getName() === "fly"){
            if(!$sender instanceof Player){
                $sender->sendMessage(self::PREFIX . TextFormat::RED . "Use this command in-game");
                return false;
            }
            if(!$sender->hasPermission("fly.command")){
                $sender->sendMessage(self::PREFIX . TextFormat::RED . "You do not have permission to use this command");
                return false;
            }
            if(!$sender->isCreative()){
                if(!$sender->getAllowFlight()){
                    $sender->setAllowFlight(true);
                    $sender->setFlying(true);
                    $sender->sendMessage($this->getConfig()->get("fly_enabled"));
                }else{
                    $sender->setAllowFlight(false);
                    $sender->setFlying(false);
                    $sender->sendMessage($this->getConfig()->get("fly_disabled"));
                }
            }else{
                $sender->sendMessage(self::PREFIX . TextFormat::RED . "You can only use this command in survival mode");
                return false;
            }
        }
        return true;
    }

    public function onDamage(EntityDamageEvent $event) : void{
        $entity = $event->getEntity();
        if($this->getConfig()->get("onDamage_FlyReset") === true){
            if($event instanceof EntityDamageByEntityEvent){
                if($entity instanceof Player){
                    $damager = $event->getDamager();
                    if(!$damager instanceof Player) return;
                    if($damager->isCreative()) return;
                    if($damager->getAllowFlight() === true){
                        $damager->sendMessage(self::PREFIX . TextFormat::DARK_RED . "Flight mode disabled due to combat");
                        $damager->setAllowFlight(false);
                        $damager->setFlying(false);
                    }
                }
            }
        }
    }
}