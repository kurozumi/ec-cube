<?php

namespace Customize\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Service\PluginService;
use Eccube\Service\SystemService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

#[AsCommand(name: 'customize:install')]
class InstallCommand extends Command
{
    protected string $projectDir;

    protected SymfonyStyle $io;

    protected Filesystem $fs;

    protected Connection $connection;

    protected SystemService $systemService;

    protected EntityManagerInterface $entityManager;

    private EccubeConfig $eccubeConfig;

    protected PluginService $pluginService;

    public function __construct(
        SystemService          $systemService,
        EntityManagerInterface $entityManager,
        EccubeConfig           $eccubeConfig,
        PluginService          $pluginService
    )
    {
        parent::__construct();

        $this->projectDir = $eccubeConfig->get('kernel.project_dir');
        $this->systemService = $systemService;
        $this->entityManager = $entityManager;
        $this->eccubeConfig = $eccubeConfig;
        $this->pluginService = $pluginService;
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->fs = new Filesystem();
        $this->connection = $this->entityManager->getConnection();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io->info('メンテナンス有効化');
        $this->systemService->switchMaintenance(true);

        $this->io->info('DBチェック');
        $databaseUrl = getenv('DATABASE_URL');
        if (false === $this->isMySQL($databaseUrl)) {
            $this->systemService->switchMaintenance();
            return Command::INVALID;
        }

        $this->io->info('DB削除');
        try {
            $connection = $this->entityManager->getConnection();
            $stmt = $connection->prepare('DROP DATABASE IF EXISTS ' . $this->connection->getDatabase());
            if ($result = $stmt->executeQuery()) {
                $this->io->success('DROP DATABASE > OK');
                $result->free();
            };
        } catch (\Exception $exception) {
            $this->io->error($exception->getMessage());
        }

        $this->io->info('EC-CUBEインストール');
        $commands = [
            ['bin/console', 'doctrine:database:create', '--if-not-exists'],
            ['bin/console', 'doctrine:schema:create'],
            ['bin/console', 'eccube:fixtures:load'],
            ['bin/console', 'cache:clear', '--no-warmup'],
        ];
        foreach ($commands as $command) {
            $this->runCommand($command);
        }

        $this->io->info('データ投入');
        $finder = Finder::create()
            ->in(__DIR__ . '/Fixtures/sql')
            ->name('*.sql');
        foreach ($finder->getIterator() as $fixture) {
            $sql = file_get_contents($fixture->getPathname());
            $command = ['bin/console', 'doctrine:query:sql', $sql];
            $this->runCommand($command);
        }

        $this->io->info('ファイル削除');
        $paths = [
            '/app/template/default/*',
            '/app/template/user_data/*',
            '/app/proxy/entity/*',
            '/html/user_data/*',
        ];
        foreach ($paths as $path) {
            $this->deleteFile($path);
        }

        $this->io->info('テンプレートファイルコピー');
        $paths = [
            '/Fixtures/template' => '/app/template',
            '/Fixtures/proxy' => '/app/proxy',
        ];
        foreach ($paths as $originDir => $targetDir) {
            $this->copyFile($originDir, $targetDir);
        }

        $this->io->info('composer install');
        $command = ['composer', 'install'];
        $this->runCommand($command);

        $this->io->info('clone plugin');
        $plugins = $this->getPlugins();
        foreach ($plugins as $code => $data) {
            if ($this->fs->exists($this->projectDir . '/app/Plugin/' . $code)) {
                continue;
            }
            $command = ['git', 'clone', 'git@github.com:kurozumi/' . $data[0] . '.git', '-b', $data[1], 'app/Plugin/' . $code];
            $this->runCommand($command);
        }

        $this->io->info('install plugin');
        foreach ($plugins as $code => $data) {
            try {
                $this->pluginService->installWithCode($code);
            } catch (\Exception $exception) {
                $this->io->error($exception->getMessage());
            }
        }

        $command = ['bin/console', 'eccube:generate:proxies'];
        $this->runCommand($command);

        $command = ['bin/console', 'doctrine:schema:update', '--force'];
        $this->runCommand($command);

        $this->io->info('メンテナンス無効化');
        $this->systemService->switchMaintenance();

        return Command::SUCCESS;
    }

    protected function deleteFile(string $path): void
    {
        foreach (glob($this->projectDir . $path) as $dir) {
            if ($dir === $this->projectDir . '/html/user_data/assets') {
                continue;
            }
            try {
                $this->fs->remove($dir);
                $this->io->success("REMOVE > " . $dir);
            } catch (\Exception $exception) {
                $this->io->error($exception->getMessage());
            }
        }
    }

    protected function copyFile(string $originDir, string $targetDir): void
    {
        foreach (glob(__DIR__ . $originDir) as $dir) {
            try {
                $this->fs->mirror($dir, $this->projectDir . $targetDir, null, ['override' => true]);
                $this->io->success('COPY > ' . $dir);
            } catch (\Exception $exception) {
                $this->io->error($exception->getMessage());
            }
        }
    }

    protected function runCommand(array $command): void
    {
        $this->io->text(sprintf('<info>Run %s</info>', implode(' ', $command)));
        $process = new Process($command);
        try {
            $process->mustRun(function ($type, $buffer) {
                $buffer = trim($buffer);
                if (empty($buffer)) {
                    return;
                }
                if (Process::ERR === $type) {
                    $this->io->error($buffer);
                } else {
                    $this->io->success($buffer);
                }
            });
        } catch (ProcessFailedException $exception) {
            $this->io->error($exception->getMessage());
        }
    }

    protected function getPlugins(): array
    {
        return [
            'CustomerGroup42' => ['CustomerGroup', '4.2'],
            'CustomerGroupPrice42' => ['CustomerGroupPrice', '4.2'],
            'CustomerGroupEntry42' => ['CustomerGroupEntry', '4.2'],
            'CustomerGroupApproval42' => ['CustomerGroupApproval', '4.2'],
            'CustomerGroupPayment42' => ['CustomerGroupPayment', '4.2'],
            'CustomerGroupRank42' => ['CustomerGroupRank', '4.2'],
            'CustomerGroupDelivery42' => ['CustomerGroupDelivery', '4.2'],
            'ApproveCustomer42' => ['ApproveCustomer4', '4.2'],
            'ProductPayment42' => ['ProductPayment4', '4.2'],
            'DeliveryFreeManagement42' => ['DeliveryFreeManagement', '4.2'],
        ];
    }

    protected function isMySQL(string $databaseUrl): bool
    {
        return str_starts_with($databaseUrl, 'mysql');
    }
}
