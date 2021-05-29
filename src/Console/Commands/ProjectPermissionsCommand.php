<?php

namespace Bridit\Serverless\Console\Commands;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ProjectPermissionsCommand extends Command
{

  protected string $name = 'project-permissions';

  protected string $description = 'Fix all files and folders permissions.';

  /**
   * In this method setup command, description, and its parameters
   */
  protected function configure()
  {
    $this->addArgument('user', InputArgument::REQUIRED, 'Owner user.');
    $this->addArgument('group', InputArgument::OPTIONAL, 'Owner group.', 'www-data');
  }

  /**
   * @return int
   */
  protected function handle()
  {

    /** --------------------------------------------------------------------
     *                               Chown
     *  -------------------------------------------------------------------- */
    $this->info('Setting project owner...');
    $this->executeCommand('chown -R ' . $this->argument('user') . ':' . $this->argument('group') . ' ' . path());

    /** --------------------------------------------------------------------
     *                             Files 664
     *  -------------------------------------------------------------------- */
    $this->info('Setting general file permissions...');
    $this->executeCommand('find ' . path() . ' -type f -exec chmod 664 {} \;');

    /** --------------------------------------------------------------------
     *                            Folders 775
     *  -------------------------------------------------------------------- */
    $this->info('Setting general folder permissions...');
    $this->executeCommand('find ' . path() . ' -type d -exec chmod 775 {} \;');

    /** --------------------------------------------------------------------
     *                          Storage and Cache
     *  -------------------------------------------------------------------- */
    $this->info('Setting "bootstrap/cache" and "storage" group and permissions...');
    $this->executeCommand('chgrp -R ' . $this->argument('group') . ' storage bootstrap/cache');
    $this->executeCommand('chmod -R ug+rwx cli storage bootstrap/cache vendor/bin vendor/bref/bref');

    return self::SUCCESS;

  }

  /**
   * @param string $command
   * @return void
   */
  protected function executeCommand(string $command): void
  {
    $process = Process::fromShellCommandline($command, path());
    $process->run();

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }
  }
}
