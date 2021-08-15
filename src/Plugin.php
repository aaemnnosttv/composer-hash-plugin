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
        $filepath = dirname($this->composer->getConfig()->getConfigSource()->getName()) . '/composer.hash';
        $contents = $this->getHashData();
        /** @see \Composer\Json\JsonFile::write */
        $json = json_encode($contents, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if (file_put_contents($filepath, $json)) {
            $this->io->write('<info>Writing hash file</info>');
        } else {
            $this->io->writeError('Failed to write hash file');
        }
    }

    /**
     * Get the hash data to write to the file.
     *
     * @return array
     */
    protected function getHashData() {
        $lock = $this->composer->getLocker()->getLockData();

        return [
            "_readme" => [
                "This file stores a reference to the lock file used by the last install/update.",
                "Read more about it at https://github.com/aaemnnosttv/composer-hash-plugin",
                "This file is @generated automatically",
            ],
            'hash' => $lock['content-hash'],
        ];
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
