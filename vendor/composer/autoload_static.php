<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita96ad7f1fea03c0bf48e8277d5bf4a24
{
    public static $files = array (
        '3917c79c5052b270641b5a200963dbc2' => __DIR__ . '/..' . '/kint-php/kint/init.php',
        '253c157292f75eb38082b5acb06f3f01' => __DIR__ . '/..' . '/nikic/fast-route/src/functions.php',
        'b33e3d135e5d9e47d845c576147bda89' => __DIR__ . '/..' . '/php-di/php-di/src/functions.php',
        '6157b075b923803e5ef157aeb43b83bd' => __DIR__ . '/..' . '/tamtamchik/simple-flash/src/function.php',
    );

    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Tamtamchik\\SimpleFlash\\' => 23,
        ),
        'R' => 
        array (
            'Repository\\' => 11,
        ),
        'P' => 
        array (
            'Psr\\Container\\' => 14,
        ),
        'M' => 
        array (
            'Models\\' => 7,
        ),
        'L' => 
        array (
            'League\\Plates\\' => 14,
            'Laravel\\SerializableClosure\\' => 28,
        ),
        'K' => 
        array (
            'Kint\\' => 5,
        ),
        'I' => 
        array (
            'Invoker\\' => 8,
        ),
        'F' => 
        array (
            'FastRoute\\' => 10,
        ),
        'D' => 
        array (
            'DI\\' => 3,
        ),
        'C' => 
        array (
            'Core\\' => 5,
            'Controllers\\' => 12,
        ),
        'A' => 
        array (
            'Aura\\SqlQuery\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Tamtamchik\\SimpleFlash\\' => 
        array (
            0 => __DIR__ . '/..' . '/tamtamchik/simple-flash/src',
        ),
        'Repository\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Repository',
        ),
        'Psr\\Container\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/container/src',
        ),
        'Models\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Models',
        ),
        'League\\Plates\\' => 
        array (
            0 => __DIR__ . '/..' . '/league/plates/src',
        ),
        'Laravel\\SerializableClosure\\' => 
        array (
            0 => __DIR__ . '/..' . '/laravel/serializable-closure/src',
        ),
        'Kint\\' => 
        array (
            0 => __DIR__ . '/..' . '/kint-php/kint/src',
        ),
        'Invoker\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-di/invoker/src',
        ),
        'FastRoute\\' => 
        array (
            0 => __DIR__ . '/..' . '/nikic/fast-route/src',
        ),
        'DI\\' => 
        array (
            0 => __DIR__ . '/..' . '/php-di/php-di/src',
        ),
        'Core\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Core',
        ),
        'Controllers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/Controllers',
        ),
        'Aura\\SqlQuery\\' => 
        array (
            0 => __DIR__ . '/..' . '/aura/sqlquery/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita96ad7f1fea03c0bf48e8277d5bf4a24::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita96ad7f1fea03c0bf48e8277d5bf4a24::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInita96ad7f1fea03c0bf48e8277d5bf4a24::$classMap;

        }, null, ClassLoader::class);
    }
}
