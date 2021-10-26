<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf5d692b0d2a7175d01d52f1004b9131f
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'SleekDB\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'SleekDB\\' => 
        array (
            0 => __DIR__ . '/..' . '/rakibtg/sleekdb/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf5d692b0d2a7175d01d52f1004b9131f::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf5d692b0d2a7175d01d52f1004b9131f::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf5d692b0d2a7175d01d52f1004b9131f::$classMap;

        }, null, ClassLoader::class);
    }
}