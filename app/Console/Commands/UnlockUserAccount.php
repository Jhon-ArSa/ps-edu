<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UnlockUserAccount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:unlock {email : El email del usuario a desbloquear}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Desbloquear una cuenta de usuario bloqueada por intentos fallidos';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("No se encontró ningún usuario con el email: {$email}");
            return Command::FAILURE;
        }

        if (!$user->isLocked()) {
            $this->info("La cuenta de {$user->name} ({$email}) no está bloqueada.");
            return Command::SUCCESS;
        }

        $user->unlockAccount();

        $this->info("✓ Cuenta desbloqueada exitosamente:");
        $this->line("  Usuario: {$user->name}");
        $this->line("  Email: {$user->email}");
        $this->line("  Intentos fallidos reseteados: {$user->failed_login_attempts} → 0");

        return Command::SUCCESS;
    }
}
