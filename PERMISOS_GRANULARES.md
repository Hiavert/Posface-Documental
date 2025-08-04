# Sistema de Permisos Granulares - Posface

## Descripción General

El sistema de permisos granulares permite un control más preciso sobre las acciones que puede realizar cada usuario en el sistema. En lugar de tener solo un permiso general de "acceso", ahora se pueden asignar permisos específicos: **Ver**, **Editar**, **Agregar** y **Eliminar**.

## Tipos de Permisos

### 1. **Ver** (`permiso_ver`)
- Permite visualizar y navegar por la funcionalidad
- Necesario para acceder a las vistas y listados
- Sin este permiso, el usuario no puede ver el módulo en el menú

### 2. **Editar** (`permiso_editar`)
- Permite modificar registros existentes
- Necesario para formularios de edición y actualización
- Requiere también el permiso de "Ver"

### 3. **Agregar** (`permiso_agregar`)
- Permite crear nuevos registros
- Necesario para formularios de creación
- Requiere también el permiso de "Ver"

### 4. **Eliminar** (`permiso_eliminar`)
- Permite eliminar registros
- Necesario para botones de eliminación
- Requiere también el permiso de "Ver"

## Estructura de Base de Datos

### Tabla `accesos`
```sql
CREATE TABLE accesos (
    id_acceso INT PRIMARY KEY AUTO_INCREMENT,
    fk_id_rol INT,
    fk_id_objeto INT,
    permiso BOOLEAN DEFAULT FALSE,           -- Permiso general (compatibilidad)
    permiso_ver BOOLEAN DEFAULT FALSE,       -- Permiso de ver
    permiso_editar BOOLEAN DEFAULT FALSE,    -- Permiso de editar
    permiso_agregar BOOLEAN DEFAULT FALSE,   -- Permiso de agregar
    permiso_eliminar BOOLEAN DEFAULT FALSE,  -- Permiso de eliminar
    FOREIGN KEY (fk_id_rol) REFERENCES roles(id_rol),
    FOREIGN KEY (fk_id_objeto) REFERENCES objetos(id_objeto)
);
```

## Uso en el Código

### 1. Verificación en Vistas (Blade)

```php
{{-- Verificar si puede ver --}}
@if(Auth::user()->puedeVer('Usuarios'))
    <a href="{{ route('usuarios.index') }}">Usuarios</a>
@endif

{{-- Verificar si puede agregar --}}
@if(Auth::user()->puedeAgregar('Usuarios'))
    <a href="{{ route('usuarios.create') }}" class="btn btn-success">
        <i class="fas fa-plus"></i> Nuevo Usuario
    </a>
@endif

{{-- Verificar si puede editar --}}
@if(Auth::user()->puedeEditar('Usuarios'))
    <a href="{{ route('usuarios.edit', $usuario->id) }}" class="btn btn-warning">
        <i class="fas fa-edit"></i> Editar
    </a>
@endif

{{-- Verificar si puede eliminar --}}
@if(Auth::user()->puedeEliminar('Usuarios'))
    <button onclick="eliminarUsuario({{ $usuario->id }})" class="btn btn-danger">
        <i class="fas fa-trash"></i> Eliminar
    </button>
@endif
```

### 2. Verificación en Controladores

```php
public function store(Request $request)
{
    // Verificar permiso de agregar
    if (!Auth::user()->puedeAgregar('Usuarios')) {
        abort(403, 'No tienes permisos para crear usuarios.');
    }
    
    // Lógica de creación...
}

public function update(Request $request, $id)
{
    // Verificar permiso de editar
    if (!Auth::user()->puedeEditar('Usuarios')) {
        abort(403, 'No tienes permisos para editar usuarios.');
    }
    
    // Lógica de actualización...
}

public function destroy($id)
{
    // Verificar permiso de eliminar
    if (!Auth::user()->puedeEliminar('Usuarios')) {
        abort(403, 'No tienes permisos para eliminar usuarios.');
    }
    
    // Lógica de eliminación...
}
```

### 3. Middleware en Rutas

```php
// Middleware para verificar permisos granulares
Route::middleware(['auth', 'granular.permission:Usuarios,ver'])->group(function () {
    Route::get('usuarios', [UserController::class, 'index'])->name('usuarios.index');
    Route::get('usuarios/{id}', [UserController::class, 'show'])->name('usuarios.show');
});

Route::middleware(['auth', 'granular.permission:Usuarios,agregar'])->group(function () {
    Route::get('usuarios/create', [UserController::class, 'create'])->name('usuarios.create');
    Route::post('usuarios', [UserController::class, 'store'])->name('usuarios.store');
});

Route::middleware(['auth', 'granular.permission:Usuarios,editar'])->group(function () {
    Route::get('usuarios/{id}/edit', [UserController::class, 'edit'])->name('usuarios.edit');
    Route::put('usuarios/{id}', [UserController::class, 'update'])->name('usuarios.update');
});

Route::middleware(['auth', 'granular.permission:Usuarios,eliminar'])->group(function () {
    Route::delete('usuarios/{id}', [UserController::class, 'destroy'])->name('usuarios.destroy');
});
```

### 4. Métodos Disponibles en Modelos

#### Modelo User
```php
$user->tienePermisoEspecifico('Usuarios', 'ver');     // Verificar permiso específico
$user->puedeVer('Usuarios');                          // Verificar permiso de ver
$user->puedeEditar('Usuarios');                       // Verificar permiso de editar
$user->puedeAgregar('Usuarios');                      // Verificar permiso de agregar
$user->puedeEliminar('Usuarios');                     // Verificar permiso de eliminar
```

#### Modelo Rol
```php
$rol->tienePermisoEspecifico('Usuarios', 'ver');      // Verificar permiso específico
$rol->puedeVer('Usuarios');                           // Verificar permiso de ver
$rol->puedeEditar('Usuarios');                        // Verificar permiso de editar
$rol->puedeAgregar('Usuarios');                       // Verificar permiso de agregar
$rol->puedeEliminar('Usuarios');                      // Verificar permiso de eliminar
```

#### Modelo Acceso
```php
$acceso->tienePermisoEspecifico('ver');               // Verificar permiso específico
$acceso->puedeVer();                                  // Verificar permiso de ver
$acceso->puedeEditar();                               // Verificar permiso de editar
$acceso->puedeAgregar();                              // Verificar permiso de agregar
$acceso->puedeEliminar();                             // Verificar permiso de eliminar
$acceso->getPermisosArray();                          // Obtener array de permisos
```

## Gestión de Permisos

### 1. Asignación de Permisos a Roles

Los permisos se asignan desde la interfaz de administración en:
`/admin/roles/{id}/permisos`

### 2. Verificación Automática

- **SuperAdmin**: Siempre tiene todos los permisos automáticamente
- **Otros roles**: Se verifica cada permiso individualmente
- **Sin permisos**: El usuario no puede acceder a la funcionalidad

### 3. Migración de Datos

Los permisos existentes se migran automáticamente:
- Si `permiso = true`, entonces `permiso_ver = true`
- Los demás permisos se inician en `false`

## Consideraciones de Seguridad

1. **Verificación en múltiples capas**: Middleware, controladores y vistas
2. **Permisos acumulativos**: Los permisos se suman entre roles
3. **SuperAdmin**: Acceso total automático
4. **Logs de acceso**: Se recomienda implementar logging de acciones

## Ejemplos de Implementación

### Botones Condicionales
```php
@if(Auth::user()->puedeAgregar('Tareas'))
    <a href="{{ route('tareas.create') }}" class="btn btn-success">
        <i class="fas fa-plus"></i> Nueva Tarea
    </a>
@endif

@if(Auth::user()->puedeEditar('Tareas'))
    <a href="{{ route('tareas.edit', $tarea->id) }}" class="btn btn-warning">
        <i class="fas fa-edit"></i> Editar
    </a>
@endif

@if(Auth::user()->puedeEliminar('Tareas'))
    <button onclick="eliminarTarea({{ $tarea->id }})" class="btn btn-danger">
        <i class="fas fa-trash"></i> Eliminar
    </button>
@endif
```

### Formularios Condicionales
```php
@if(Auth::user()->puedeEditar('Perfil'))
    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        @method('PUT')
        <!-- Campos del formulario -->
        <button type="submit" class="btn btn-primary">Actualizar Perfil</button>
    </form>
@else
    <div class="alert alert-info">
        No tienes permisos para editar tu perfil.
    </div>
@endif
```

## Compatibilidad

El sistema mantiene compatibilidad con el sistema anterior:
- El campo `permiso` sigue funcionando para verificaciones generales
- Los métodos `tienePermiso()` siguen disponibles
- La migración es automática y no destructiva 