<?php
require_once 'config.php';
require_once 'controllers/plantillaController.php';
require_once 'models/usuarios.php';
$usuario= new UsuariosModel();
$usuario->validateSession();
$plantilla = new Plantilla();
date_default_timezone_set('America/Bogota');

##### PERMISOS #####

require_once 'models/permisos.php';
require_once 'models/admin.php';
$id_user = $_SESSION['idusuario']; //Se obtiene el id de usuario de la sesión actual

// Verificar los permisos del usuario
$permisos = new PermisosModel();
$configuracion = $permisos->getPermiso(1, $id_user);
$usuarios = $permisos->getPermiso(2, $id_user);
$clientes = $permisos->getPermiso(3, $id_user);
$productos = $permisos->getPermiso(4, $id_user);
$ventas = $permisos->getPermiso(5, $id_user);
$nueva_venta = $permisos->getPermiso(6, $id_user);
$compras = $permisos->getPermiso(7, $id_user);
$nueva_compra = $permisos->getPermiso(8, $id_user);
$proveedor = $permisos->getPermiso(9, $id_user);

##### FIN PERMISOS #####

// Verifica si el usuario tiene una caja abierta hoy
$admin = new AdminModel();
$fechaHoy = date('Y-m-d');
$cajaAbierta = $admin->checkCajaAbierta($id_user, $fechaHoy);

if ($cajaAbierta) {
    $_SESSION['caja_abierta'] = true;
    $_SESSION['id_info_caja'] = $cajaAbierta['id_info_caja']; // Guarda el id_info_caja en la sesión
} else {
    $_SESSION['caja_abierta'] = false;
    $_SESSION['id_info_caja'] = null; // Limpia id_info_caja si no hay caja abierta
}

// Declaración de idInfoCaja para JavaScript (solo una vez)
echo "<script>const idInfoCaja = " . json_encode($_SESSION['id_info_caja'] ?? null) . ";</script>";

// Carga de la vista principal 
require_once 'views/includes/header.php';

if (isset($_GET['pagina'])) {
    if (empty($_GET['pagina'])) {
        $plantilla->index();
    } else {
        try {
            $archivo = $_GET['pagina'];
            if ($archivo == 'usuarios' && !empty($usuarios)) {
                $plantilla->usuarios();
            } else if ($archivo == 'configuracion' && !empty($configuracion)) {
                $plantilla->configuracion();
            } else if ($archivo == 'clientes' && !empty($clientes)) {
                $plantilla->clientes();
            } else if ($archivo == 'proveedor' && !empty($proveedor)) {
                $plantilla->proveedor();
            } else if ($archivo == 'productos' && !empty($productos)) {
                $plantilla->productos();
            } else if ($archivo == 'ventas' && !empty($nueva_venta)) {
                $plantilla->ventas();
            } else if ($archivo == 'historial' && !empty($ventas)) {
                $plantilla->historial();
            } else if ($archivo == 'reporte' && !empty($ventas)) {
                $plantilla->reporte();
            } else if ($archivo == 'compras' && !empty($nueva_compra)) {
                $plantilla->compras();
            } else if ($archivo == 'historial_compras' && !empty($ventas)) {
                $plantilla->historial_compras();
            } else if ($archivo == 'reporte_compra' && !empty($compras)) {
                $plantilla->reporte_compra();
            } else {
                $plantilla->notFound();
            }
        } catch (\Throwable $th) {
            $plantilla->notFound();
        }
    }
} else {
    $plantilla->index();
}

require_once 'views/includes/footer.php';
