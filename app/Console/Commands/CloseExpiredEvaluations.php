<?php

namespace App\Console\Commands;

use App\Models\Evaluation;
use Illuminate\Console\Command;

class CloseExpiredEvaluations extends Command
{
    protected $signature = 'evaluations:close-expired';

    protected $description = 'Cierra automáticamente las evaluaciones que han llegado a su fecha de cierre';

    public function handle()
    {
        $evaluations = Evaluation::where('status', 'published')
            ->whereNotNull('closes_at')
            ->where('closes_at', '<=', now())
            ->get();

        $count = 0;
        foreach ($evaluations as $evaluation) {
            $evaluation->update(['status' => 'closed']);
            $count++;
            $this->info("Evaluación cerrada: {$evaluation->title} (ID: {$evaluation->id})");
        }

        if ($count === 0) {
            $this->info('No hay evaluaciones para cerrar.');
        } else {
            $this->info("Se cerraron {$count} evaluación(es).");
        }

        return 0;
    }
}
