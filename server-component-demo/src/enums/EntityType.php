<?php

enum EntityType: string
{
    case Player = 'player';
    case HealthPack = 'health_pack';
    case PoisonPack = 'poison_pack';
    case SpeedBoost = 'speed_boost';
    
    /**
     * Returns the category of the entity for collision grouping
     */
    public function getCategory(): EntityCategory
    {
        return match($this) {
            self::Player => EntityCategory::Actor,
            self::HealthPack, 
            self::PoisonPack,
            self::SpeedBoost  => EntityCategory::Collectible, // self::SpeedBoost 
        };
    }
}

/**
 * Categories for grouping entities to prevent same-type collisions
 */
enum EntityCategory: string
{
    case Actor = 'actor';      // Moving entities like Player
    case Collectible = 'collectible';  // Static items to be collected
    case Obstacle = 'obstacle';  // Static barriers (for future use)
}