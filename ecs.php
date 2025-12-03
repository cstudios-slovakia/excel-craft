<?php

declare(strict_types=1);

use craft\ecs\SetList;
use Symplify\EasyCodingStandard\Config\ECSConfig;

/**
 * @param ECSConfig $ecsConfig
 * @return void
 */
return static function(ECSConfig $ecsConfig): void {
    $ecsConfig->paths([
        __DIR__ . '/src',
        __FILE__,
    ]);

    $ecsConfig->parallel();
    $ecsConfig->sets([SetList::CRAFT_CMS_4]); // for Craft 4 projects
};
