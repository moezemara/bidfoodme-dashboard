<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita5f7e78ac597e8f678ec65ec2bac7ff2
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Src\\User\\Paths\\' => 15,
            'Src\\User\\' => 9,
            'Src\\Auth\\Paths\\' => 15,
            'Src\\Auth\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Src\\User\\Paths\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/user/paths',
        ),
        'Src\\User\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/user',
        ),
        'Src\\Auth\\Paths\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/auth/paths',
        ),
        'Src\\Auth\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src/auth',
        ),
    );

    public static $prefixesPsr0 = array (
        'M' => 
        array (
            'Mustache' => 
            array (
                0 => __DIR__ . '/..' . '/mustache/mustache/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita5f7e78ac597e8f678ec65ec2bac7ff2::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita5f7e78ac597e8f678ec65ec2bac7ff2::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInita5f7e78ac597e8f678ec65ec2bac7ff2::$prefixesPsr0;
            $loader->classMap = ComposerStaticInita5f7e78ac597e8f678ec65ec2bac7ff2::$classMap;

        }, null, ClassLoader::class);
    }
}
