# Cuentas por Cobrar – Backend

API REST desarrollada con Laravel para gestionar cuentas por cobrar, terceros, estados de cuenta y depósitos, facilitando el seguimiento y control de obligaciones pendientes.

## Funcionalidades

- **Autenticación:** inicio y cierre de sesión, consulta del usuario autenticado y renovación de tokens mediante JWT.
- **Gestión de terceros:** registro y actualización de terceros.
- **Gestión de cuentas por cobrar:** consulta, registro, actualización y eliminación de cuentas.
- **Estados de cuenta:** consulta de cuentas pendientes por tercero.
- **Gestión de depósitos:** registro y eliminación de depósitos.
- **Dashboard:** consulta de información consolidada para indicadores y seguimiento.

## Tecnologías utilizadas

- PHP 8.1+
- Laravel 10
- MySQL
- JSON Web Tokens (JWT)
- Laravel JWT Auth (Tymon)
- API REST

## Arquitectura

Backend independiente que expone servicios mediante una API REST para la comunicación con el frontend desarrollado en Vue.js 3.

La autenticación se implementa mediante JWT, protegiendo los endpoints que requieren acceso autorizado.

## Instalación y configuración

1. Clonar el repositorio:

   ```bash
   git clone <URL_DEL_REPOSITORIO>
   ```

2. Ingresar al directorio del proyecto:

   ```bash
   cd cuentas-por-cobrar-backend
   ```

3. Instalar dependencias:

   ```bash
   composer install
   ```

4. Crear el archivo de configuración:

   ```bash
   cp .env.example .env
   ```

5. Generar la clave de aplicación:

   ```bash
   php artisan key:generate
   ```

6. Configurar en `.env` la conexión a la base de datos MySQL.

