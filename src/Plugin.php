<?php

namespace ComposerHashPlugin;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class Plugin implements PluginInterface, EventSubscriberInterface
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
     * The default file name to write the hash to.
     */
    const DEFAULT_FILE = 'composer.hash';

    /**
     * The key used in the project's extra configuration to override the default file.
     */
    const CONFIG_KEY = 'hash-file';

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
        $dir = dirname($this->composer->getConfig()->getConfigSource()->getName());
        $extra = $this->composer->getPackage()->getExtra();
        $filename = ! empty($extra[self::CONFIG_KEY]) ? $extra[self::CONFIG_KEY] : self::DEFAULT_FILE;
        $filepath = "$dir/$filename";

        if (! is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        if (file_put_contents($filepath, $lock['content-hash'])) {
            $this->io->write("<info>Wrote $filename</info>");
        } else {
            $this->io->writeError("Failed to write $filename");
        }
    }
}