<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit348bb802bed681979f6ca12391a3a79c
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Curl\\' => 5,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Curl\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-curl-class/php-curl-class/src/Curl',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit348bb802bed681979f6ca12391a3a79c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit348bb802bed681979f6ca12391a3a79c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit348bb802bed681979f6ca12391a3a79c::$classMap;

        }, null, ClassLoader::class);
    }
}
