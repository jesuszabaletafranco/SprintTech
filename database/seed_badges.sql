-- ============================================================
-- SprintTech — Seed: Badges (3 por curso × 6 cursos = 18)
-- ============================================================
USE sprinttech;

INSERT IGNORE INTO badges (course_id, nombre, descripcion, icono, color, checkpoint_pct, nivel, emoji) VALUES
-- Curso 1: IA Generativa
(1,'Explorador IA','Completaste tu primer módulo de IA Generativa','fa-seedling','#00d4ff',25,'Explorador','🌱'),
(1,'Practicante IA','Superaste la mitad del curso de IA Generativa','fa-brain','#7c3aed',50,'Practicante','🧠'),
(1,'Experto IA','¡Completaste el curso de IA Generativa!','fa-robot','#f59e0b',100,'Experto','🤖'),
-- Curso 2: Agentes IA
(2,'Explorador Agentes','Completaste tu primer módulo de Agentes de IA','fa-seedling','#7c3aed',25,'Explorador','🌱'),
(2,'Practicante Agentes','Superaste la mitad del curso de Agentes','fa-microchip','#00d4ff',50,'Practicante','⚙️'),
(2,'Experto Agentes','¡Completaste el curso de Agentes de IA!','fa-microchip','#f59e0b',100,'Experto','🤖'),
-- Curso 3: Visión y NLP
(3,'Explorador Visión','Completaste tu primer módulo de Visión y NLP','fa-seedling','#06b6d4',25,'Explorador','🌱'),
(3,'Practicante NLP','Superaste la mitad del curso de Visión y NLP','fa-eye','#7c3aed',50,'Practicante','👁️'),
(3,'Experto NLP','¡Completaste el curso de Visión por Computador!','fa-eye','#f59e0b',100,'Experto','🔬'),
-- Curso 4: Flutter y React Native
(4,'Explorador Apps','Completaste tu primer módulo de Apps Móviles','fa-seedling','#10b981',25,'Explorador','🌱'),
(4,'Practicante Apps','Superaste la mitad del curso de Apps Móviles','fa-mobile-screen','#00d4ff',50,'Practicante','📱'),
(4,'Experto Apps','¡Completaste el curso de Apps Multiplataforma!','fa-mobile-screen','#f59e0b',100,'Experto','🚀'),
-- Curso 5: PWA y Edge AI
(5,'Explorador PWA','Completaste tu primer módulo de PWA y Edge AI','fa-seedling','#f59e0b',25,'Explorador','🌱'),
(5,'Practicante PWA','Superaste la mitad del curso de PWA','fa-wifi','#10b981',50,'Practicante','🌐'),
(5,'Experto Edge AI','¡Completaste el curso de PWA e IA en el Borde!','fa-wifi','#f59e0b',100,'Experto','⚡'),
-- Curso 6: IoT Industrial
(6,'Explorador IoT','Completaste tu primer módulo de IoT Industrial','fa-seedling','#ef4444',25,'Explorador','🌱'),
(6,'Practicante IoT','Superaste la mitad del curso de IIoT','fa-network-wired','#f59e0b',50,'Practicante','🔌'),
(6,'Experto IoT','¡Completaste el curso de IoT Industrial!','fa-network-wired','#ef4444',100,'Experto','🏭');
