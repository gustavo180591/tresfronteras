Quiero que actúes como un experto en PHP 7.4, MySQL y desarrollo de paneles de administración limpios, rápidos y fáciles de usar en entornos de evento deportivo.

Nombre del proyecto: tresfronteras  
Contexto: es un sistema local (servidor en red local) para gestionar el evento deportivo Tresfronteras. Necesitamos:

- Administrar el fixture de todos los partidos.
- Llevar una lista de pedidos de fotos.
- Controlar la recaudación en tiempo real.
- Navegación rápida, limpia y súper intuitiva.

Tecnologías:
- PHP 7.4 (sin frameworks pesados, estilo MVC simple).
- MySQL local.
- HTML5 + CSS3 + JavaScript.
- Se puede usar Bootstrap para lograr un diseño moderno.
- Proyecto organizado en carpetas claras: `public`, `app`, `views`, `config`, `controllers`, `models`, etc.

===========================================================
1) DASHBOARD PRINCIPAL "Panel Tresfronteras"
===========================================================

Diseñar un tablero inicial con:

- Header con título “Panel Tresfronteras”.
- Navbar con accesos a:
  • Fixture  
  • Pedidos de fotos  
  • Recaudación

- Tarjetas resumen (cards):
  • Total de partidos cargados  
  • Total de pedidos de fotos  
  • Recaudación total  
  • Recaudación discriminada por:
      - efectivo
      - transferencia

========================================
BÚSQUEDA RÁPIDA — BARRA GLOBAL
========================================

Agregar una barra de búsqueda global fija en el header donde el operador pueda escribir:

- Número de pedido  
- Nombre de la persona  
- Nombre de equipo  

Y el sistema lo lleve automáticamente al partido o pedido correspondiente.

Debe ser veloz y tolerante a errores ortográficos simples.

=====================================================
2) MÓDULO FIXTURE – Gestión completa del evento
=====================================================

Vista para administrar todos los partidos. Debe incluir:

- Tabla/listado con:
  • ID del partido  
  • Categoría  
  • Equipo A  
  • Equipo B  
  • Fecha/hora  
  • Estado (pendiente, en juego, finalizado)  
  • Resultado (goles A / goles B)  
  • Acciones (editar / eliminar)

- Botón “+ Agregar partido” con formulario para:
  • Categoría  
  • Tipo de torneo (por puntos o eliminación directa)  
  • Equipo A  
  • Equipo B  
  • Fecha/hora  
  • Cancha (opcional)  
  • Observaciones (opcional)

LÓGICA SEGÚN TIPO DE TORNEO:

A) Torneo por puntos → generar tabla de posiciones dinámica:
   - Equipo  
   - PJ / PG / PE / PP  
   - GF / GC / DG  
   - Puntos  
   *Actualización automática según resultados.*

B) Eliminación directa → generar llaves:
   - Octavos / Cuartos / Semis / Final  
   - Permitir avanzar ganadores automáticamente.

=========================================================
3) MÓDULO "PEDIDOS DE FOTOS" – Gestión rápida en evento
=========================================================

Cada pedido debe almacenar:

- Número de pedido (ID incremental)
- Equipo (opcional)
- Categoría (opcional)
- Número dorsal (opcional)
- Cancha (opcional)
- Hora (opcional)
- Nombre del cliente (opcional)
- Archivos solicitados (ej: IMG_001.jpg, IMG_002.jpg) (opcional)
- Cantidad de fotos (opcional)
- Forma de pago (Efectivo / Transferencia / No especificado) (opcional)
- Teléfono (opcional)
- Pago (Pagado / No pagado / Pendiente) (opcional)
- Entrega (Entregado / No entregado / En proceso) (opcional)
- Fecha/hora del pedido
- Observaciones (opcional)

IMPORTANTE: Ningún campo debe ser obligatorio para permitir máxima flexibilidad en la toma de pedidos durante el evento.

Funcionalidades:

- Crear nuevo pedido (formulario simple y rápido).
- Listar pedidos con:
  • Buscador por nombre, partido, teléfono o número.
  • Filtros por pago / entrega.
  • Botones rápidos para cambiar estado sin recargar página.
- Edición completa desde una vista dedicada.

========================================
INDICADORES VISUALES IMPORTANTES
========================================

- Pagado → verde  
- No pagado → rojo  
- No entregado → amarillo / naranja  
- Entregado → azul

Esto permite visualizar el estado de cada pedido en menos de 1 segundo.

=====================================================
RESPALDO / EXPORTACIÓN A CSV
=====================================================

Agregar opción para exportar:

- Lista completa de pedidos de fotos  
- Recaudación filtrada por fecha y forma de pago  

Exportar en CSV con codificación UTF-8 y separador estándar.

=====================================================
4) MÓDULO "RECAUDACIÓN"
=====================================================

Vista que muestre:

- Total recaudado
- Total por forma de pago
  • efectivo  
  • transferencia  
- Listado de pedidos pagados
- Filtros por:
  • rango de fechas  
  • forma de pago  
- Botón para exportar CSV

El sistema debe permitir configurar un **precio por foto**, almacenado en tabla configuración:
  Monto = cantidad_fotos × precio_unitario

=====================================================
5) BASE DE DATOS (MySQL)
=====================================================

Diseñar el modelo con tablas:

- `categorias`
- `tipos_torneo` (puntos / eliminación)
- `partidos`
- `pedidos_fotos`
- `configuracion`

Incluir claves primarias, foráneas, índices, reglas de integridad y tipos correctos (`INT`, `VARCHAR`, `DATETIME`, `ENUM`).

=====================================================
6) DISEÑO YO-LO-USO-EN-UN-EVENTO (PRIORIDAD)
=====================================================

El sistema debe ser extremadamente ágil y claro:

- Botones grandes
- Pocas cosas por pantalla
- Tipografía grande
- Tablas limpias
- Selects rápidos
- Estados cambiables en un clic (toggle o select inline)
- Colores claros y funcionales
- Navegación fluida y sin recarga innecesaria

=====================================================
7) ORDENAMIENTO
=====================================================

Implementar ordenamiento por:
  • fecha  
  • categoría  
  • estado  
  • alfabéticamente  

=====================================================
8) ENTREGABLES
=====================================================

Solicito que generes:

1. Modelo de base de datos completo (tablas + relaciones + SQL).
2. Estructura profesional del proyecto PHP 7.4 (MVC simple).
3. Archivos iniciales:
   - index.php
   - config/database.php
   - layout base del dashboard (header, navbar, footer, barra de búsqueda global).
4. CRUDs completos de:
   - Partidos (fixture)
   - Pedidos de fotos
5. Lógica completa de:
   - Tabla de posiciones
   - Llaves por eliminación
   - Cálculo de recaudación
6. Listados, filtros, exportaciones CSV.
7. Vistas limpias, modernas y pensadas para velocidad en campo.