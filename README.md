# Sistema de Gestión Tresfronteras

Sistema local para la gestión integral del evento deportivo Tresfronteras, incluyendo administración de partidos, pedidos de fotos y control de recaudación.

## 🚀 Características Principales

- **Panel de control** con estadísticas en tiempo real
- **Gestión completa de partidos** con seguimiento de resultados
- **Sistema de pedidos de fotos** integrado
- **Control de recaudación** con desglose por métodos de pago
- **Búsqueda global** rápida e intuitiva
- **Interfaz limpia** y fácil de usar

## 🛠 Tecnologías Utilizadas

- PHP 7.4
- MySQL
- HTML5 + CSS3 + JavaScript
- Bootstrap 5 (para el diseño responsivo)
- Arquitectura MVC personalizada

## 📋 Módulos Principales

### 1. Panel de Control
- Vista general del estado del evento
- Tarjetas resumen con métricas clave
- Acceso rápido a todas las funcionalidades

### 2. Gestión de Partidos
- Creación y edición de partidos
- Seguimiento de resultados en tiempo real
- Generación automática de tablas de posiciones y llaves
- Filtrado por categoría y estado

### 3. Pedidos de Fotos
- Registro de pedidos con seguimiento
- Asociación con partidos específicos
- Gestión de estados de pedidos
- Generación de reportes

### 4. Control de Recaudación
- Registro de pagos (efectivo/transferencia)
- Reportes de recaudación
- Historial de transacciones

## 🚀 Instalación

1. Clonar el repositorio:
   ```bash
   git clone [url-del-repositorio]
   ```
2. Configurar la base de datos MySQL (ver `config/database.php`)
3. Importar el esquema de la base de datos
4. Configurar el servidor web para apuntar al directorio `public/`
5. Configurar permisos de escritura en los directorios necesarios

## 📁 Estructura del Proyecto

```
tresfronteras/
├── app/              # Lógica de la aplicación
│   ├── controllers/  # Controladores
│   ├── models/       # Modelos de datos
│   └── core/         # Núcleo del sistema
├── config/           # Archivos de configuración
├── public/           # Punto de entrada público
│   ├── css/          # Hojas de estilo
│   ├── js/           # Scripts JavaScript
│   └── uploads/      # Archivos subidos
├── views/            # Vistas de la aplicación
└── README.md         # Este archivo
```

## 📄 Licencia

Este proyecto es de uso interno para el evento Tresfronteras.
