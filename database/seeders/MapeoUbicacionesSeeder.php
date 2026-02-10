<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MapeoUbicacion;

class MapeoUbicacionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Limpiamos la tabla antes (opcional, pero útil para evitar duplicados si se corre varias veces)
        MapeoUbicacion::truncate();

        $mapa = [
            9 => ['Villa Ballester', 'San Martin', 'Malaver', 'Florida', 'Olivos', 'Martinez', 'San Isidro', 'San Fernando'],
            5 => ['Ramos Mejia', 'San Justo', 'Lomas del Mirador', 'Ciudad Evita', 'Aldo Bonzi', 'Laferrere'],
            6 => ['Moron', 'Haedo', 'Castelar', 'Caseros', 'Ciudadela', 'Hurlingham', 'Ituzaingo', 'Santos Lugares'],
            1 => ['CABA', 'Ciudad Autonoma de Buenos Aires', 'Palermo', 'Belgrano', 'Caballito', 'Flores'],
            2 => ['Avellaneda', 'Lanus', 'Gerli', 'Quilmes', 'Bernal', 'Wilde'],
            3 => ['Lomas de Zamora', 'Banfield', 'Temperley', 'Berazategui', 'Florencio Varela'],
            8 => ['Tigre', 'General Pacheco', 'Don Torcuato', 'Benavidez', 'Nordelta'],
            10 => ['Pilar', 'Del Viso', 'Villa Rosa', 'Fatima'],
            11 => ['Bahia Blanca', 'Mar del Plata', 'Tandil', 'Pergamino', 'Olavarria', 'Junin', 'Azul', 'Necochea'],
            35 => ['La Plata', 'Ensenada', 'Berisso'],
            37 => ['Lujan'],
            38 => ['Escobar'],
            39 => ['Campana'],
        ];

        foreach ($mapa as $destino_id => $ciudades) {
            foreach ($ciudades as $ciudad) {
                MapeoUbicacion::create([
                    'ciudad_detectada' => $ciudad,
                    'destino_id' => $destino_id
                ]);
            }
        }
    }
}
