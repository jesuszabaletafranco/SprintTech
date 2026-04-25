-- ============================================================
-- SprintTech — Migración de Aprendizaje Interactivo
-- Ejecutar DESPUÉS de schema.sql
-- ============================================================
USE sprinttech;

CREATE TABLE IF NOT EXISTS lesson_content (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  module_id         INT NOT NULL UNIQUE,
  contenido_html    LONGTEXT,
  video_url         VARCHAR(500),
  video_titulo      VARCHAR(255),
  duracion_minutos  INT DEFAULT 10,
  FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS quizzes (
  id                 INT AUTO_INCREMENT PRIMARY KEY,
  module_id          INT NOT NULL,
  pregunta           TEXT NOT NULL,
  opcion_a           VARCHAR(500) NOT NULL,
  opcion_b           VARCHAR(500) NOT NULL,
  opcion_c           VARCHAR(500),
  opcion_d           VARCHAR(500),
  respuesta_correcta CHAR(1) NOT NULL COMMENT 'a|b|c|d',
  explicacion        TEXT,
  orden              INT DEFAULT 0,
  FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS quiz_results (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  user_id          INT NOT NULL,
  module_id        INT NOT NULL,
  course_id        INT NOT NULL,
  puntaje          INT NOT NULL DEFAULT 0,
  total_preguntas  INT NOT NULL DEFAULT 0,
  aprobado         TINYINT(1) DEFAULT 0,
  intentos         INT DEFAULT 1,
  fecha_ultimo     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY uq_user_module (user_id, module_id),
  FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
  FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS module_completions (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  user_id          INT NOT NULL,
  module_id        INT NOT NULL,
  course_id        INT NOT NULL,
  fecha_completado DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_user_module_comp (user_id, module_id),
  FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
  FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS badges (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  course_id      INT NOT NULL,
  nombre         VARCHAR(100) NOT NULL,
  descripcion    VARCHAR(255),
  icono          VARCHAR(50) DEFAULT 'fa-medal',
  color          VARCHAR(20) DEFAULT '#f59e0b',
  checkpoint_pct INT NOT NULL,
  nivel          VARCHAR(50) DEFAULT 'Explorador',
  emoji          VARCHAR(10) DEFAULT '🏅',
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS user_badges (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  user_id         INT NOT NULL,
  badge_id        INT NOT NULL,
  course_id       INT NOT NULL,
  fecha_obtenido  DATETIME DEFAULT CURRENT_TIMESTAMP,
  compartido      TINYINT(1) DEFAULT 0,
  UNIQUE KEY uq_user_badge (user_id, badge_id),
  FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
  FOREIGN KEY (badge_id)  REFERENCES badges(id)  ON DELETE CASCADE,
  FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS sentiment_logs (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  user_id       INT NOT NULL,
  module_id     INT NOT NULL,
  texto         TEXT NOT NULL,
  score         DECIMAL(5,2) DEFAULT 0.00,
  clasificacion VARCHAR(20) DEFAULT 'neutro',
  sugerencia    TEXT,
  fecha         DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE CASCADE,
  FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
