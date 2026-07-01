<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Notifications\WelcomeUserNotification;
use App\Models\User;

class TestEmailConfiguration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email? : Email de destino (opcional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba la configuración de envío de correos electrónicos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando configuración de correo...');
        $this->newLine();

        // Verificar configuración
        $mailer = config('mail.default');
        $host = config('mail.mailers.smtp.host');
        $port = config('mail.mailers.smtp.port');
        $username = config('mail.mailers.smtp.username');
        $from = config('mail.from.address');
        $fromName = config('mail.from.name');

        $this->table(
            ['Configuración', 'Valor'],
            [
                ['Mailer por defecto', $mailer],
                ['Host SMTP', $host],
                ['Puerto SMTP', $port],
                ['Usuario SMTP', $username],
                ['Email remitente', $from],
                ['Nombre remitente', $fromName],
            ]
        );

        $this->newLine();

        if ($mailer === 'log') {
            $this->warn('⚠️  MAIL_MAILER está configurado como "log".');
            $this->warn('Los emails se guardarán en storage/logs/laravel.log pero no se enviarán.');
            $this->newLine();
        }

        $testEmail = $this->argument('email') ?? $this->ask('¿A qué dirección de email quieres enviar la prueba?', $username);

        if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            $this->error('❌ Email inválido: ' . $testEmail);
            return Command::FAILURE;
        }

        $this->info("📤 Enviando email de prueba a: {$testEmail}");
        $this->newLine();

        try {
            // Método 1: Email simple de prueba
            $this->info('Método 1: Email simple...');
            
            Mail::raw('Este es un email de prueba desde PS-EDU FAEDU.', function ($message) use ($testEmail) {
                $message->to($testEmail)
                        ->subject('🧪 Prueba de Email - PS-EDU');
            });

            $this->info('✅ Email simple enviado correctamente');
            $this->newLine();

            // Método 2: Notificación de bienvenida
            if ($this->confirm('¿Quieres probar también la notificación de bienvenida?', true)) {
                $this->info('Método 2: Notificación de bienvenida...');
                
                // Crear usuario temporal para prueba
                $testUser = new User([
                    'name' => 'Usuario de Prueba',
                    'email' => $testEmail,
                    'role' => 'admin',
                ]);

                $testUser->notify(new WelcomeUserNotification(
                    'Usuario de Prueba',
                    $testEmail,
                    'Password123!',
                    'admin'
                ));

                $this->info('✅ Notificación de bienvenida enviada correctamente');
            }

            $this->newLine();
            $this->info('🎉 ¡Prueba de email completada exitosamente!');
            $this->info("📧 Revisa la bandeja de entrada de: {$testEmail}");
            
            if ($mailer === 'log') {
                $this->newLine();
                $this->warn('💡 Como MAIL_MAILER=log, el email está en: storage/logs/laravel.log');
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Error al enviar email:');
            $this->error($e->getMessage());
            $this->newLine();

            // Diagnóstico adicional
            $this->warn('🔍 Diagnóstico:');
            $this->newLine();

            if (str_contains($e->getMessage(), 'Connection refused')) {
                $this->warn('• No se pudo conectar al servidor SMTP');
                $this->warn('• Verifica que el host y puerto sean correctos');
                $this->warn('• Verifica que el firewall permita conexiones al puerto ' . $port);
            }

            if (str_contains($e->getMessage(), 'authentication failed')) {
                $this->warn('• Autenticación fallida');
                $this->warn('• Verifica el usuario y contraseña SMTP');
                $this->warn('• Para Gmail, usa una "App Password" en lugar de tu contraseña normal');
            }

            if (str_contains($e->getMessage(), 'Could not instantiate mail function')) {
                $this->warn('• La función mail() de PHP no está disponible');
                $this->warn('• Verifica la configuración de PHP en el servidor');
            }

            $this->newLine();
            $this->info('💡 Soluciones sugeridas:');
            $this->line('1. Verifica las credenciales en el archivo .env');
            $this->line('2. Para Gmail, genera una App Password: https://myaccount.google.com/apppasswords');
            $this->line('3. Verifica que el puerto 587 esté abierto en tu servidor');
            $this->line('4. Revisa los logs: storage/logs/laravel.log');

            return Command::FAILURE;
        }
    }
}
