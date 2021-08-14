<?php

namespace ComposerHash;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;

class Plugin implements PluginInterface, EventSubscriberInterface, Capable
{
    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @var IOInterface
     */
    protected $io;

    /**
     * Apply plugin modifications to Composer
     *
     * @param Composer    $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'post-install-cmd' => 'writeHash',
            'post-update-cmd' => 'writeHash',
        ];
    }

    /**
     * Write the current composer hash to a file.
     */
    public function writeHash()
    {
        $lock = $this->composer->getLocker()->getLockData();
        $filepath = dirname($this->composer->getConfig()->getConfigSource()->getName()) . '/composer.hash';

        if (file_put_contents($filepath, $lock['content-hash'])) {
            $this->io->write('<info>Writing hash file</info>');
        } else {
            $this->io->writeError('Failed to write hash file');
        }
    }

    /**
     * @inheritDoc
     */
    public function getCapabilities()
    {
        return [
            'Composer\Plugin\Capability\CommandProvider' => CommandProvider::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public function deactivate(Composer $composer, IOInterface $io)
    {
    }

    /**
     * @inheritDoc
     */
    public function uninstall(Composer $composer, IOInterface $io)
    {
    }
}
