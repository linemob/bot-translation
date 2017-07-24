<?php

namespace LineMob\Bot\Translation;

use Google\Cloud\Translate\TranslateClient;
use LineMob\Bot\Translation\Command\ChangeTargetLanguageCommand;
use LineMob\Bot\Translation\Command\TranslationCommand;
use LineMob\Bot\Translation\Middleware\ChangeTargetLanguageMiddleware;
use LineMob\Bot\Translation\Middleware\DefineUserLanguageMiddleware;
use LineMob\Bot\Translation\Middleware\TranslateMiddleware;
use LineMob\Core\Middleware\CleanInputTextMiddleware;
use LineMob\Core\Middleware\ClearActivedCmdMiddleware;
use LineMob\Core\Middleware\DummyStorageConnectMiddleware;
use LineMob\Core\Middleware\DummyStoragePersistMiddleware;
use LineMob\Core\Middleware\DumpLogMiddleware;
use LineMob\Core\Middleware\StoreActivedCmdMiddleware;
use LineMob\Core\QuickStart;
use LineMob\Core\Receiver;

class Setup
{
    /**
     * @param array $config
     *
     * @return Receiver
     */
    public static function demo(array $config)
    {
        $googleClient = new TranslateClient([
            'projectId' => $config['google_project_id'],
            'key' => $config['google_api_key'],
            'target' => $config['google_default_locale'],
        ]);

        $quickStart = new QuickStart([
            new CleanInputTextMiddleware(),
            new ClearActivedCmdMiddleware(),
            new DummyStorageConnectMiddleware(),
            new StoreActivedCmdMiddleware(),
            new DefineUserLanguageMiddleware($config['google_default_locale'], $config['google_fallback_locale']),
            new ChangeTargetLanguageMiddleware($googleClient),
            new TranslateMiddleware($googleClient),
            new DummyStoragePersistMiddleware(),
            new DumpLogMiddleware($config['isDebug']),
        ]);

        return $quickStart
            ->addCommand(ChangeTargetLanguageCommand::class)
            ->addCommand(TranslationCommand::class, true)

            ->setup($config['line_channel_token'], $config['line_channel_secret'], ['verify' => !$config['isDebug']])
        ;
    }
}
