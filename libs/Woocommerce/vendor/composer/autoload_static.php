<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6d5408759de4c86595be0f7ae4f638ae
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Automattic\\WooCommerce\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Automattic\\WooCommerce\\' => 
        array (
            0 => __DIR__ . '/..' . '/automattic/woocommerce/src/WooCommerce',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6d5408759de4c86595be0f7ae4f638ae::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6d5408759de4c86595be0f7ae4f638ae::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6d5408759de4c86595be0f7ae4f638ae::$classMap;

        }, null, ClassLoader::class);
    }
}
