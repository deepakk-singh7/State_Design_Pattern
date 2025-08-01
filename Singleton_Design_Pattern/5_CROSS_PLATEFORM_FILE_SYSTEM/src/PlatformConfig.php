<?php 

class PlatformConf{
    private static ?string $platform = null; 

    public static function setPlatform(string $platform):void{
        self::$platform = strtoupper($platform);
        echo 'Platform set to '. self::$platform . PHP_EOL;
    }

    public static function getPlatform():string{
        return self::$platform;
    }
    public static function resetPlatform():void{
        self::$platform = null;
    }
}