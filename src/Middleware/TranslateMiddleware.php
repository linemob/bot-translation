<?php

namespace LineMob\Bot\Translation\Middleware;

use Google\Cloud\Translate\TranslateClient;
use League\Tactician\Middleware;
use LineMob\Bot\Translation\Command\AbstractTranslateCommand;
use LineMob\Bot\Translation\Command\TranslationCommand;
use LineMob\Core\Template\TextTemplate;

class TranslateMiddleware implements Middleware
{
    /**
     * @var TranslateClient
     */
    private $client;

    public function __construct(TranslateClient $client) {
        $this->client = $client;
    }

    /**
     * @param AbstractTranslateCommand $command
     * @throws \Exception
     */
    private function flipTargetLanguage(AbstractTranslateCommand $command)
    {
        $targetLanguageCode = $command->targetLanguageCode;
        $sourceLanguageCode = $this->client->detectLanguage($command->input->text)['languageCode'];

        if ($targetLanguageCode === $sourceLanguageCode) {
            $targetLanguageCode = $command->sourceLanguageCode;
        }

        if ($targetLanguageCode === $sourceLanguageCode) {
            $targetLanguageCode = $command->targetLanguageCode;
        }

        $command->sourceLanguageCode = $sourceLanguageCode;
        $command->targetLanguageCode = $targetLanguageCode;
    }

    /**
     * @param AbstractTranslateCommand $command
     *
     * {@inheritdoc}
     */
    public function execute($command, callable $next)
    {
        if (!$command instanceof TranslationCommand) {
            return $next($command);
        }

        $this->flipTargetLanguage($command);

        $command->message = new TextTemplate();

        if (2 > mb_strlen($command->input->text)) {
            $command->message->text = 'Enter more than 2 words.';
        } else {
            $translation = $this->client->translate(
                $command->input->text,
                [
                    'target' => $command->targetLanguageCode,
                    'model' => 'base',
                ]
            );

            $command->message->text = $translation['text'];
        }

        return $next($command);
    }
}
