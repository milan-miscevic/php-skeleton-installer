<?php

declare(strict_types=1);

namespace Mmm\SkeletonInstaller;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Json\JsonFile;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;

class Plugin implements PluginInterface, EventSubscriberInterface
{
    const PLUGIN_NAME = 'milan-miscevic/skeleton-installer';
    const PREFIX = 'milan-miscevic/';

    private Composer $composer;
    private IOInterface $io;

    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::POST_CREATE_PROJECT_CMD => 'configure',
        ];
    }

    public function configure(Event $event): void
    {
        $composerFile = Factory::getComposerFile();
        $composerJson = new JsonFile($composerFile);
        $json = $composerJson->read();

        $answer = $this->io->ask('Enter the name of the project: ');
        $json['name'] = static::PREFIX . $answer;

        unset($json['require'][static::PLUGIN_NAME]);
        unset($json['scripts'][ScriptEvents::POST_CREATE_PROJECT_CMD]);

        $composerJson->write($json);
    }
}
