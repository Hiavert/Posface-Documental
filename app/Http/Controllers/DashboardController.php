<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $modulosDisponibles = [];

        if (Auth::user()->can('ver-Terna')) {
            $modulosDisponibles[] = [
                'nombre' => 'Pagos Terna',
                'descripcion' => 'Gestión de pagos académicos',
                'icono' => 'fas fa-money-check-alt',
                'color'  => 'success',
                'ruta'   => 'terna.admin.index'
            ];
        }

        if (Auth::user()->can('ver-TernaAux')) {
            $modulosDisponibles[] = [
                'nombre' => 'Pagos de Terna (Aux)',
                'descripcion' => 'Gestión de pagos asistente',
                'icono' => 'fas fa-cash-register',
                'color'  => 'lime',
                'ruta'   => 'terna.asistente.index'
            ];
        }

        if (Auth::user()->can('ver-GestionTesis')) {
            $modulosDisponibles[] = [
                'nombre' => 'Gestión de Tesis',
                'descripcion' => 'Registro y control de tesis',
                'icono' => 'fas fa-book-reader',
                'color'  => 'purple',
                'ruta'   => 'tesis.index'
            ];
        }

        if (Auth::user()->can('ver-Documentos')) {
            $modulosDisponibles[] = [
                'nombre' => 'Gestión de Documentos',
                'descripcion' => 'Registro y administración de oficios',
                'icono' => 'fas fa-folder-open',
                'color'  => 'info',
                'ruta'   => 'documentos.gestor'
            ];
        }

        if (Auth::user()->can('ver-Recepcion')) {
            $modulosDisponibles[] = [
                'nombre' => 'Documentos Recibidos',
                'descripcion' => 'Recepción de documentos',
                'icono' => 'fas fa-inbox',
                'color'  => 'primary',
                'ruta'   => 'documentos.recepcion'
            ];
        }

        if (Auth::user()->can('ver-TareasDocumentales')) {
            $modulosDisponibles[] = [
                'nombre' => 'Tareas Documentales',
                'descripcion' => 'Gestión de tareas administrativas',
                'icono' => 'fas fa-tasks',
                'color'  => 'orange',
                'ruta'   => 'tareas.index'
            ];
        }

        if (Auth::user()->can('ver-GestorRendimiento')) {
            $modulosDisponibles[] = [
                'nombre' => 'Gestor Rendimiento',
                'descripcion' => 'Estadísticas y reportes',
                'icono' => 'fas fa-chart-pie',
                'color'  => 'warning',
                'ruta'   => 'espacio'
            ];
        }

        if (Auth::user()->can('ver-GestionAcuses')) {
            $modulosDisponibles[] = [
                'nombre' => 'Gestión de Acuses',
                'descripcion' => 'Entrega de documentos y artículos',
                'icono' => 'fas fa-file-signature',
                'color'  => 'cyan',
                'ruta'   => 'acuses.index'
            ];
        }

        if (Auth::user()->can('ver-Usuarios')) {
            $modulosDisponibles[] = [
                'nombre' => 'Gestión de Usuarios',
                'descripcion' => 'Administración de usuarios y roles',
                'icono' => 'fas fa-user-shield',
                'color'  => 'danger',
                'ruta'   => 'usuarios.index'
            ];
        }

        if (Auth::user()->can('ver-Roles')) {
            $modulosDisponibles[] = [
                'nombre' => 'Roles',
                'descripcion' => 'Control de roles del sistema',
                'icono' => 'fas fa-id-badge',
                'color'  => 'indigo',
                'ruta'   => 'roles.index'
            ];
        }

        if (Auth::user()->can('ver-Bitacora')) {
            $modulosDisponibles[] = [
                'nombre' => 'Bitácora',
                'descripcion' => 'Registro de actividades',
                'icono' => 'fas fa-history',
                'color'  => 'teal',
                'ruta'   => 'bitacora.index'
            ];
        }

        return view('dashboard', compact('modulosDisponibles'));
    }
}
