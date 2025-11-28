<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Asignar rol de administrador del restaurante al usuario daniel@gmail.com
$user = App\Models\User::where('email', 'daniel@gmail.com')->first();

if ($user) {
    $user->assignRole('administrador');
    echo "✅ Rol 'administrador' asignado exitosamente a: {$user->name} ({$user->email})\n";
    echo "Ahora puedes iniciar sesión con:\n";
    echo "  Email: daniel@gmail.com\n";
    echo "  Contraseña: 12345678\n";
} else {
    echo "❌ Usuario no encontrado\n";
}
