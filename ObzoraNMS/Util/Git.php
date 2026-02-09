<?php
namespace ObzoraNMS\Util;

use App\Facades\ObzoraConfig;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Str;
use ObzoraNMS\Traits\RuntimeClassCache;
use Symfony\Component\Process\Process;

class Git
{
    use RuntimeClassCache;

    /** @var string */
    private $install_dir;

    public function __construct(int $cache = 0)
    {
        $this->runtimeCacheExternalTTL = $cache;
        $this->install_dir = realpath(__DIR__ . '/../..');
    }

    public static function make(int $cache = 0): Git
    {
        try {
            $git = app()->make('git'); // get the singleton
            $git->runtimeCacheExternalTTL = $cache;

            return $git;
        } catch (BindingResolutionException $e) {
            return new static($cache); // no container, just return a regular instance
        }
    }

    public function isAvailable(): bool
    {
        return $this->cacheGet('isAvailable', function () {
            return $this->repoPresent() && $this->binaryExists();
        });
    }

    public function repoPresent(): bool
    {
        return $this->cacheGet('repoPresent', function () {
            return is_dir("$this->install_dir/.git");
        });
    }

    public function binaryExists(): bool
    {
        return $this->cacheGet('binaryExists', function () {
            return $this->run('version', [])->isSuccessful();
        });
    }

    public function tag(): string
    {
        return $this->cacheGet('tag', function () {
            return $this->isAvailable()
                ? rtrim($this->run('describe', ['--tags'])->getOutput())
                : '';
        });
    }

    public function shortTag(): string
    {
        return $this->cacheGet('shortTag', function () {
            return $this->isAvailable()
                ? rtrim($this->run('describe', ['--tags', '--abbrev=0'])->getOutput())
                : '';
        });
    }

    /**
     * Returns the commit hash of the local HEAD commit
     */
    public function commitHash(): string
    {
        return $this->headCommit()[0] ?? '';
    }

    /**
     * Get the date of the local HEAD commit
     */
    public function commitDate(): string
    {
        return $this->headCommit()[1] ?? '';
    }

    /**
     * Get the current branch
     */
    public function branch(): string
    {
        return $this->cacheGet('branch', function () {
            return $this->isAvailable()
                ? rtrim($this->run('rev-parse', ['--abbrev-ref', 'HEAD'])->getOutput())
                : '';
        });
    }

    /**
     * Detect if there are local uncommitted changes.
     */
    public function hasChanges(): bool
    {
        return $this->cacheGet('hasChanges', function () {
            return $this->isAvailable() && ! $this->run('diff-index', ['--quiet', 'HEAD'])->isSuccessful();
        });
    }

    /**
     * Note: It assumes origin/master points to github.com/obzora/obzora for this to work.
     */
    public function isOfficialCommits(): bool
    {
        return $this->cacheGet('isOfficialCommits', function () {
            if (! $this->isAvailable()) {
                return false;
            }

            $process = $this->run('log', ['--exit-code', '--max-count=1', '--oneline', 'origin/master..HEAD']);

            return $process->isSuccessful();
        });
    }

    /**
     * Get the url of the origin remote
     */
    public function remoteUrl(): string
    {
        return $this->cacheGet('remoteUrl', function () {
            return $this->isAvailable()
                ? rtrim($this->run('remote', ['get-url', 'origin'])->getOutput())
                : '';
        });
    }

    public function message(): string
    {
        return $this->cacheGet('remoteUrl', function () {
            return $this->isAvailable()
                ? rtrim($this->run('log', ['--pretty=format:%s', '-n', '1'])->getOutput())
                : '';
        });
    }

    public function log(int $lines = 10): string
    {
        return $this->cacheGet('changelog' . $lines, function () use ($lines) {
            return $this->isAvailable()
                ? rtrim($this->run('log', ['-' . $lines])->getOutput())
                : '';
        });
    }

    /**
     * Fetches the remote commit hash from the github api if on the daily release channel
     */
    public function remoteHash(): string
    {
        return $this->remoteCommit()['sha'] ?? '';
    }

    /**
     * Fetches the remote commit from the github api if on the daily release channel
     */
    private function remoteCommit(): array
    {
        return $this->cacheGet('remoteCommit', function () {
            if ($this->isAvailable()) {
                try {
                    return (array) Http::client()->get(ObzoraConfig::get('github_api') . 'commits/master')->json();
                } catch (ConnectionException $e) {
                }
            }

            return [];
        });
    }

    private function headCommit(): array
    {
        return $this->cacheGet('headCommit', function () {
            if (! $this->isAvailable()) {
                return [];
            }

            $version_process = $this->run('show', ['--quiet', '--pretty=%H|%ct']);

            // failed due to permissions issue
            if ($version_process->getExitCode() == 128 && Str::startsWith($version_process->getErrorOutput(), 'fatal: unsafe repository')) {
                $this->run('config', ['--global', '--add', 'safe.directory', $this->install_dir]); // try to fix
                $version_process = $this->run('show', ['--quiet', '--pretty=%H|%ct']); // and try again
            }

            return explode('|', rtrim($version_process->getOutput()));
        });
    }

    private function run(string $command, array $options): Process
    {
        $version_process = new Process(array_merge(['git', $command], $options), $this->install_dir);
        $version_process->run();

        return $version_process;
    }
}
