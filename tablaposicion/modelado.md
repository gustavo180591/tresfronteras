2) Modelo de datos mínimo (MySQL)

Te conviene tener estas tablas (o equivalentes):

categorias

id, nombre (2014/2015/2016/2017)

equipos

id, nombre

grupos

id, categoria_id, letra (A/B/C/D)

grupo_equipos

grupo_id, equipo_id (relación)

partidos

id

categoria_id

grupo_id (nullable en eliminatorias)

fase ENUM('grupos','cuartos','semis','final')

fecha_hora, cancha

equipo_a_id, equipo_b_id

goles_a, goles_b (nullable hasta que cargues)

estado ENUM('programado','jugado')