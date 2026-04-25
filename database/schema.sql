-- ============================================================
-- SprintTech LearningWithIA — Schema MySQL
-- ============================================================

CREATE DATABASE IF NOT EXISTS sprinttech 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE sprinttech;

-- ─────────────────────────────────────────
-- TABLA: users
-- ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nombre        VARCHAR(100) NOT NULL,
  apellido      VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  password      VARCHAR(255) NOT NULL,
  rol           ENUM('estudiante','admin') DEFAULT 'estudiante',
  fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────
-- TABLA: courses
-- ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS courses (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  nombre          VARCHAR(200) NOT NULL,
  descripcion     TEXT,
  objetivo        TEXT,
  duracion_horas  INT NOT NULL,
  nivel           VARCHAR(50) DEFAULT 'Básico',
  herramientas    VARCHAR(255),
  color_accent    VARCHAR(20) DEFAULT '#00d4ff',
  icono           VARCHAR(50) DEFAULT 'fa-brain',
  orden           INT DEFAULT 0,
  activo          TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────
-- TABLA: modules
-- ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS modules (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  course_id   INT NOT NULL,
  orden       INT NOT NULL,
  titulo      VARCHAR(200) NOT NULL,
  descripcion TEXT,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────
-- TABLA: course_challenges
-- ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS course_challenges (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  course_id           INT NOT NULL,
  titulo              VARCHAR(200) NOT NULL,
  descripcion         TEXT,
  actividad           TEXT,
  criterio_1          VARCHAR(200),
  criterio_2          VARCHAR(200),
  criterio_3          VARCHAR(200),
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────
-- TABLA: enrollments
-- ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS enrollments (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  user_id         INT NOT NULL,
  course_id       INT NOT NULL,
  fecha_matricula DATETIME DEFAULT CURRENT_TIMESTAMP,
  progreso        INT DEFAULT 0 COMMENT 'Porcentaje 0-100',
  activo          TINYINT(1) DEFAULT 1,
  UNIQUE KEY unique_enrollment (user_id, course_id),
  FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────
-- TABLA: certifications
-- ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS certifications (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  user_id         INT NOT NULL,
  course_id       INT NOT NULL,
  fecha_emision   DATETIME DEFAULT CURRENT_TIMESTAMP,
  codigo_cert     VARCHAR(50) NOT NULL UNIQUE,
  FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DATOS SEMILLA — 6 Cursos de Tecnologías Emergentes
-- ============================================================

INSERT INTO courses (id, nombre, descripcion, objetivo, duracion_horas, nivel, herramientas, color_accent, icono, orden) VALUES
(1,
 'IA Generativa en la Vida Real',
 'Aprende a usar las herramientas de inteligencia artificial generativa más avanzadas del mundo para resolver tareas cotidianas y profesionales de forma eficiente.',
 'Usar IA generativa para resolver tareas cotidianas con herramientas como ChatGPT, Claude, Gemini y Llama.',
 12, 'Básico', 'ChatGPT, Claude, Gemini, Llama',
 '#00d4ff', 'fa-robot', 1),

(2,
 'Agentes de IA Autónomos',
 'Explora el fascinante mundo de los sistemas inteligentes que ejecutan tareas de manera automática, desde automatización básica hasta flujos complejos de decisión.',
 'Comprender cómo funcionan sistemas que ejecutan tareas automáticamente y diseñar agentes sencillos.',
 10, 'Básico', 'N8N, AutoGPT, LangChain básico',
 '#7c3aed', 'fa-microchip', 2),

(3,
 'Visión por Computador y NLP',
 'Descubre cómo la IA "ve" imágenes y "entiende" el lenguaje humano, con aplicaciones prácticas en reconocimiento facial, chatbots y análisis de texto.',
 'Entender cómo la IA procesa imágenes y texto con aplicaciones reales usando herramientas básicas.',
 12, 'Básico', 'Google Vision, Hugging Face, OpenCV básico',
 '#06b6d4', 'fa-eye', 3),

(4,
 'Desarrollo de Apps (Flutter y React Native)',
 'Crea aplicaciones móviles multiplataforma que funcionen en iOS y Android con un solo código fuente, usando los frameworks más demandados del mercado.',
 'Crear una app básica multiplataforma usando Flutter y React Native.',
 16, 'Básico', 'Flutter, React Native, VS Code',
 '#10b981', 'fa-mobile-screen', 4),

(5,
 'PWA e IA en el Borde (Edge AI)',
 'Aprende a construir aplicaciones web modernas que funcionen sin conexión a internet y descubre cómo la IA puede ejecutarse directamente en dispositivos.',
 'Entender cómo funcionan las aplicaciones web progresivas y la IA en dispositivos edge.',
 12, 'Básico', 'Workbox, TensorFlow Lite, PWABuilder',
 '#f59e0b', 'fa-wifi', 5),

(6,
 'IoT Industrial (IIoT)',
 'Comprende cómo los dispositivos conectados transforman fábricas y ciudades inteligentes, con sensores, datos en tiempo real y soluciones de automatización.',
 'Comprender cómo dispositivos conectados mejoran procesos industriales y urbanos.',
 12, 'Básico', 'Arduino, Raspberry Pi, MQTT, Node-RED',
 '#ef4444', 'fa-network-wired', 6);

-- ─────────────────────────────────────────
-- MÓDULOS — Curso 1: IA Generativa
-- ─────────────────────────────────────────
INSERT INTO modules (course_id, orden, titulo, descripcion) VALUES
(1, 1, '¿Qué es la IA Generativa?', 'Fundamentos de la inteligencia artificial generativa, modelos de lenguaje y cómo funcionan internamente.'),
(1, 2, 'Cómo crear buenos prompts', 'Técnicas de prompt engineering para obtener resultados precisos y útiles de cualquier herramienta de IA.'),
(1, 3, 'Aplicaciones prácticas', 'Uso de ChatGPT, Claude, Gemini y Llama para redacción, análisis, código, imágenes y más.'),
(1, 4, 'Uso responsable de la IA', 'Ética, sesgos, derechos de autor y buenas prácticas en el uso de IA generativa.');

-- ─────────────────────────────────────────
-- MÓDULOS — Curso 2: Agentes de IA
-- ─────────────────────────────────────────
INSERT INTO modules (course_id, orden, titulo, descripcion) VALUES
(2, 1, '¿Qué es un Agente de IA?', 'Definición, arquitectura y tipos de agentes inteligentes autónomos.'),
(2, 2, 'Automatización básica', 'Automatizar tareas como responder correos, organizar calendarios y gestionar información.'),
(2, 3, 'Flujo: entrada → proceso → acción', 'Diseño de flujos de decisión: cómo un agente recibe información y ejecuta acciones.'),
(2, 4, 'Casos reales simples', 'Ejemplos de agentes usados en empresas reales: soporte, ventas y productividad.');

-- ─────────────────────────────────────────
-- MÓDULOS — Curso 3: Visión y NLP
-- ─────────────────────────────────────────
INSERT INTO modules (course_id, orden, titulo, descripcion) VALUES
(3, 1, 'Introducción a Visión por Computador', 'Cómo la IA procesa y entiende imágenes, videos y reconocimiento de objetos.'),
(3, 2, 'Introducción a NLP', 'Procesamiento de Lenguaje Natural: análisis de sentimientos, traducción y generación de texto.'),
(3, 3, 'Ejemplos cotidianos', 'Reconocimiento facial, chatbots de atención, filtros de spam y más aplicaciones del día a día.'),
(3, 4, 'Uso con herramientas básicas', 'Práctica con Google Vision API y modelos de Hugging Face para análisis de imágenes y texto.');

-- ─────────────────────────────────────────
-- MÓDULOS — Curso 4: Apps Flutter + RN
-- ─────────────────────────────────────────
INSERT INTO modules (course_id, orden, titulo, descripcion) VALUES
(4, 1, '¿Qué es una app multiplataforma?', 'Diferencias entre apps nativas, híbridas y multiplataforma. Ventajas del desarrollo unificado.'),
(4, 2, 'Introducción a Flutter', 'Instalación, estructura de proyecto, widgets básicos y primer app en Flutter.'),
(4, 3, 'Introducción a React Native', 'Configuración, componentes básicos, navegación y primer app en React Native.'),
(4, 4, 'Diseño de una app simple', 'Diseño UI/UX básico: layouts, colores, tipografía y experiencia de usuario en móviles.');

-- ─────────────────────────────────────────
-- MÓDULOS — Curso 5: PWA y Edge AI
-- ─────────────────────────────────────────
INSERT INTO modules (course_id, orden, titulo, descripcion) VALUES
(5, 1, '¿Qué es una PWA?', 'Progressive Web Apps: definición, características y diferencias con apps nativas.'),
(5, 2, 'Introducción a Edge AI', 'IA en el borde: modelos que corren en dispositivos sin depender de la nube.'),
(5, 3, 'Ventajas: offline y rapidez', 'Service Workers, caché, notificaciones push y funcionamiento sin conexión.'),
(5, 4, 'Casos prácticos', 'Apps comunitarias, monitoreo de campo y soluciones para zonas con conectividad limitada.');

-- ─────────────────────────────────────────
-- MÓDULOS — Curso 6: IoT Industrial
-- ─────────────────────────────────────────
INSERT INTO modules (course_id, orden, titulo, descripcion) VALUES
(6, 1, '¿Qué es IoT e IIoT?', 'Internet de las Cosas e Internet Industrial de las Cosas: conceptos, diferencias y ecosistema.'),
(6, 2, 'Sensores y datos', 'Tipos de sensores, protocolos de comunicación y flujo de datos en sistemas IoT.'),
(6, 3, 'Aplicaciones en ciudades inteligentes', 'Gestión de tráfico, agua, energía y residuos con tecnología IIoT.'),
(6, 4, 'Casos simples', 'Proyectos básicos: semáforo inteligente, monitor de temperatura y control de riego.');

-- ─────────────────────────────────────────
-- RETOS — Todos los cursos
-- ─────────────────────────────────────────
INSERT INTO course_challenges (course_id, titulo, descripcion, actividad, criterio_1, criterio_2, criterio_3) VALUES
(1,
 'Crear un producto con IA',
 'Usa herramientas de IA generativa para crear un producto real: un texto, plan de negocios o contenido multimedia.',
 'Selecciona una herramienta de IA (ChatGPT, Claude o Gemini), crea un prompt detallado y genera un producto de valor.',
 'Uso correcto de las herramientas', 'Calidad y utilidad del resultado', 'Aplicación en un contexto real'),

(2,
 'Diseñar un agente sencillo',
 'Diseña un asistente de tareas automatizado que resuelva un problema cotidiano.',
 'El estudiante diseña los pasos de automatización para una tarea específica: "Si [condición], entonces [acción]".',
 'Claridad del flujo diseñado', 'Lógica y coherencia de los pasos', 'Aplicabilidad en la vida real'),

(3,
 'Sistema que describe imágenes o resume textos',
 'Crea una solución simple que use visión por computador o NLP para procesar información automáticamente.',
 'Analiza imágenes con IA (Google Vision o similar) y/o genera resúmenes automáticos de textos con NLP.',
 'Comprensión del concepto', 'Uso correcto de herramientas', 'Resultado funcional y presentado'),

(4,
 'Diseñar una app sencilla',
 'Diseña y prototipa una app de eventos deportivos o control personal usando Flutter o React Native.',
 'Crea la interfaz básica (pantalla principal) con navegación, colores y al menos 2 pantallas funcionales.',
 'Funcionalidad básica operativa', 'Calidad del diseño visual', 'Creatividad e innovación'),

(5,
 'App comunitaria que funcione offline',
 'Diseña una solución PWA para una comunidad con conectividad limitada.',
 'Simula una app que funcione sin internet: define el contenido en caché, las funciones offline y el valor para la comunidad.',
 'Comprensión del concepto PWA/Edge', 'Aplicación práctica propuesta', 'Innovación y pertinencia social'),

(6,
 'Propuesta de solución IIoT para comunidad',
 'Diseña un sistema IoT que solucione un problema real en tu entorno.',
 'Identifica un problema (agua, energía, tráfico), propón sensores, flujo de datos y solución implementable.',
 'Claridad del problema identificado', 'Calidad de la solución propuesta', 'Impacto social y viabilidad');
