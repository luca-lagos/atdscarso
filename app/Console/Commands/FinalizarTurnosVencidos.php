<?php

namespace App\Console\Commands;

use App\Models\Turnos_sala;
use App\Models\Turnos_tv;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FinalizarTurnosVencidos extends Command
{
    protected $signature = 'turnos:finalizar-vencidos';

    protected $description = 'Marca como "finalizado" los turnos de sala y TV cuya fecha + hora_fin ya pasó';

    public function handle()
    {
        $this->info('🔍 Buscando turnos vencidos...');

        // Turnos de Sala
        $salaActualizados = Turnos_sala::whereIn('estado', ['activo', 'pendiente'])
            ->whereRaw("CONCAT(fecha_turno::text, ' ', hora_fin::text)::timestamp < NOW()")
            ->update(['estado' => 'finalizado']);

        $this->info("✅ Turnos de Sala finalizados: {$salaActualizados}");

        // Turnos de TV
        $tvActualizados = Turnos_tv::whereIn('estado', ['activo', 'pendiente'])
            ->whereRaw("CONCAT(fecha_turno::text, ' ', hora_fin::text)::timestamp < NOW()")
            ->update(['estado' => 'finalizado']);

        $this->info("✅ Turnos de TV finalizados: {$tvActualizados}");

        $this->info("🎉 Proceso completado. Total: " . ($salaActualizados + $tvActualizados) . " turnos finalizados.");

        return Command::SUCCESS;
    }
}
