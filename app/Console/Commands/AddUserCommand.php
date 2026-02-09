<?php
namespace App\Console\Commands;

use App\Console\LnmsCommand;
use App\Facades\ObzoraConfig;
use App\Models\User;
use Illuminate\Validation\Rule;
use ObzoraNMS\Authentication\LegacyAuth;
use Spatie\Permission\Models\Role;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class AddUserCommand extends LnmsCommand
{
    protected $name = 'user:add';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->setDescription(__('commands.user:add.description'));

        $this->addArgument('username', InputArgument::REQUIRED);
        $this->addOption('password', 'p', InputOption::VALUE_REQUIRED);
        $this->addOption('role', 'r', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, __('commands.user:add.options.role', ['roles' => '[user, global-read, admin]']), ['user']);
        $this->addOption('email', 'e', InputOption::VALUE_REQUIRED);
        $this->addOption('full-name', 'l', InputOption::VALUE_REQUIRED);
        $this->addOption('descr', 's', InputOption::VALUE_REQUIRED);
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (ObzoraConfig::get('auth_mechanism') != 'mysql') {
            $this->warn(__('commands.user:add.wrong-auth'));
        }

        $roles = Role::query()->pluck('name')
            ->whenEmpty(fn () => collect(['admin', 'global-read', 'user']));

        $this->validate([
            'username' => ['required', Rule::unique('users', 'username')->where('auth_type', 'mysql')],
            'email' => 'nullable|email',
            'role.*' => Rule::in($roles),
        ]);

        // set get password
        $password = $this->option('password');
        if (! $password) {
            $password = $this->secret(__('commands.user:add.password-request'));
        }

        $user = new User([
            'username' => $this->argument('username'),
            'descr' => $this->option('descr'),
            'email' => $this->option('email'),
            'realname' => $this->option('full-name'),
            'auth_type' => 'mysql',
        ]);

        $user->setPassword($password);
        $user->save();
        $user->assignRole($this->option('role'));

        $user->auth_id = (string) LegacyAuth::get()->getUserid($user->username) ?: $user->user_id;
        $user->save();

        $this->info(__('commands.user:add.success', ['username' => $user->username]));

        return 0;
    }
}
