# 🏆 Sistema de Gestión Tresfronteras

## 📌 Descripción del Proyecto

`tresfronteras` es un sistema en **PHP 7.4 + MySQL** diseñado para la gestión integral de eventos deportivos.

### Características Principales

- Dashboard principal con métricas en tiempo real
- Gestión completa del fixture de partidos
- Módulo de pedidos de fotos con seguimiento de estados (pagado/entregado)
- Control de recaudación con desglose por formas de pago
- Búsqueda global rápida y eficiente
- Exportación de datos a CSV
- Interfaz intuitiva y optimizada para uso en eventos en vivo

## � Estructura del Proyecto

```
tresfronteras/
│
├── public/                   # Archivos públicos accesibles
│   ├── index.php            # Punto de entrada principal
│   ├── css/                 # Hojas de estilo
│   │   └── style.css
│   ├── js/                  # Scripts del lado del cliente
│   │   └── app.js
│   └── assets/              # Recursos estáticos
│       └── logo.png
│
├── app/                     # Lógica de la aplicación
│   ├── controllers/         # Controladores
│   │   ├── DashboardController.php
│   │   ├── PartidosController.php
│   │   ├── PedidosController.php
│   │   └── RecaudacionController.php
│   │
│   ├── models/              # Modelos de datos
│   │   ├── Partido.php
│   │   ├── Pedido.php
│   │   ├── Categoria.php
│   │   ├── TipoTorneo.php
│   │   └── Configuracion.php
│   │
│   └── views/               # Vistas de la aplicación
│       ├── layout/          # Plantillas base
│       │   ├── header.php
│       │   ├── navbar.php
│       │   └── footer.php
│       ├── dashboard/       # Vistas del panel
│       │   └── index.php
│       ├── partidos/        # Gestión de partidos
│       │   ├── index.php
│       │   ├── crear.php
│       │   └── editar.php
│       ├── pedidos/         # Gestión de pedidos
│       │   ├── index.php
│       │   ├── crear.php
│       │   └── editar.php
│       └── recaudacion/     # Control de ingresos
│           └── index.php
│
├── config/                  # Configuraciones
│   ├── database.php         # Configuración de la base de datos
│   └── config.php           # Configuración general
│
└── sql/                     # Esquemas SQL
    └── schema.sql           # Estructura completa de la base de datos
```

## � Archivos Principales

### Punto de Entrada
- `public/index.php` - Enruta las peticiones a los controladores correspondientes

### Configuración
- `config/database.php` - Configuración de conexión a MySQL usando PDO
- `config/config.php` - Configuraciones generales de la aplicación

### Vistas Principales
- `app/views/layout/header.php` - Cabecera común con estilos y scripts
- `app/views/layout/navbar.php` - Menú de navegación principal

### Controladores
- `DashboardController.php` - Gestiona el panel principal con estadísticas
- `PartidosController.php` - CRUD de partidos y lógica de torneos
- `PedidosController.php` - Gestión de pedidos con exportación CSV
- `RecaudacionController.php` - Control de ingresos y reportes

### Base de Datos
- `sql/schema.sql` - Estructura completa con las tablas:
  - `categorias`
  - `tipos_torneo`
  - `partidos`
  - `pedidos_fotos`
  - `configuracion`

## 🚀 Instalación

1. Clonar el repositorio en el servidor local:
   ```bash
   git clone [url-del-repositorio] tresfronteras
   ```

2. Crear una base de datos MySQL e importar la estructura:
   ```bash
   mysql -u usuario -p nombre_base_de_datos < sql/schema.sql
   ```

3. Configurar las credenciales de la base de datos:
   ```php
   // config/database.php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'nombre_base_de_datos');
   define('DB_USER', 'usuario');
   define('DB_PASS', 'contraseña');
   ```

4. Acceder a la aplicación desde el navegador:
   ```
   http://localhost/tresfronteras/public/
   ```

## 🎯 Objetivos del Sistema

Diseñado para ser rápido, confiable y simple, optimizado para uso en eventos deportivos:

- **Interfaz intuitiva** con botones grandes y accesibles
- **Flujo de trabajo optimizado** para uso bajo presión
- **Gestión visual** con códigos de colores claros para estados
- **Búsqueda global** de acceso rápido
- **Exportación rápida** de datos a CSV
- **Diseño responsivo** que funciona en diferentes dispositivos

## 📄 Licencia

Este proyecto es de uso interno para el evento Tresfronteras.