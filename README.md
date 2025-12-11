📌 Descripción del proyecto

tresfronteras es un sistema en PHP 7.4 + MySQL diseñado para gestionar un evento deportivo.
Incluye:

Dashboard principal con métricas del evento.

Gestión completa del fixture de partidos.

Módulo de pedidos de fotos con estados pagado/entregado.

Recaudación total y por forma de pago.

Búsqueda global rápida.

Exportación a CSV.

Interfaz simple, veloz y pensada para uso en un evento real.

📂 Estructura de carpetas
tresfronteras/
│
├── public/
│   ├── index.php
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── app.js
│   └── assets/
│       └── logo.png
│
├── app/
│   ├── controllers/
│   │   ├── DashboardController.php
│   │   ├── PartidosController.php
│   │   ├── PedidosController.php
│   │   └── RecaudacionController.php
│   │
│   ├── models/
│   │   ├── Partido.php
│   │   ├── Pedido.php
│   │   ├── Categoria.php
│   │   ├── TipoTorneo.php
│   │   └── Configuracion.php
│   │
│   └── views/
│       ├── layout/
│       │   ├── header.php
│       │   ├── navbar.php
│       │   └── footer.php
│       │
│       ├── dashboard/
│       │   └── index.php
│       │
│       ├── partidos/
│       │   ├── index.php
│       │   ├── crear.php
│       │   └── editar.php
│       │
│       ├── pedidos/
│       │   ├── index.php
│       │   ├── crear.php
│       │   └── editar.php
│       │
│       └── recaudacion/
│           └── index.php
│
├── config/
│   ├── database.php
│   └── config.php
│
└── sql/
    └── schema.sql

📌 Archivos principales
public/index.php

Punto de entrada. Enruta a los controladores.

config/database.php

Conexión MySQL (PDO) lista para incluir en modelos.

app/views/layout/header.php

Contiene <head>, estilos, scripts iniciales y barra global de búsqueda.

app/views/layout/navbar.php

Menú principal: Dashboard / Fixture / Pedidos / Recaudación.

DashboardController.php

Calcula totales y renderiza tarjetas del panel.

PartidosController.php

CRUD de partidos + lógica para torneos por puntos y eliminación.

PedidosController.php

CRUD de pedidos + cambio rápido de estados + exportación CSV.

RecaudacionController.php

Totales, filtros y exportación CSV.

sql/schema.sql

Contiene todas las tablas necesarias según el prompt:

categorias

tipos_torneo

partidos

pedidos_fotos

configuracion

▶️ Cómo iniciar el proyecto

Clonar el repositorio en el servidor local.

Crear base de datos MySQL e importar sql/schema.sql.

Configurar credenciales en config/database.php.

Acceder desde navegador:

http://localhost/tresfronteras/public/

🎯 Objetivo del sistema

Ser una herramienta rápida, confiable y simple, optimizada para trabajar bajo presión durante un evento deportivo:

Botones grandes

Pocas acciones por pantalla

Colores claros para estados

Búsqueda global inmediata

Exportación rápida a CSV

Flujo limpio para cargar partidos y pedidos