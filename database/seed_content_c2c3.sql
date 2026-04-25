-- ============================================================
-- SprintTech — Seed: Contenido cursos 2, 3 (módulos 5-12)
-- ============================================================
USE sprinttech;

-- ─── CURSO 2: Agentes de IA (modules 5-8) ───
INSERT IGNORE INTO lesson_content (module_id, contenido_html, video_url, video_titulo, duracion_minutos) VALUES
(5,
'<h3>¿Qué es un Agente de Inteligencia Artificial?</h3>
<p>Un <strong>agente de IA</strong> es un sistema capaz de percibir su entorno, tomar decisiones y ejecutar acciones de forma autónoma para alcanzar un objetivo.</p>
<h4>Arquitectura básica de un agente</h4>
<ul>
  <li><strong>Percepción:</strong> recibe información del entorno (texto, datos, APIs)</li>
  <li><strong>Razonamiento:</strong> procesa la información con un LLM</li>
  <li><strong>Memoria:</strong> recuerda contexto previo (corto y largo plazo)</li>
  <li><strong>Acción:</strong> ejecuta tareas: buscar en web, enviar emails, escribir código</li>
</ul>
<h4>Tipos de agentes</h4>
<ul>
  <li><strong>Reactivos:</strong> responden directamente a estímulos sin memoria</li>
  <li><strong>Deliberativos:</strong> planifican antes de actuar</li>
  <li><strong>Multi-agente:</strong> varios agentes colaboran entre sí</li>
</ul>
<div class="lesson-tip">💡 Herramientas como AutoGPT, AgentGPT y LangChain permiten crear agentes con capacidades reales de automatización.</div>',
'https://www.youtube.com/embed/vbzFSXrXNMU',
'Agentes de IA: Qué son y cómo funcionan',14),

(6,
'<h3>Automatización Básica con IA</h3>
<p>La automatización con IA permite que tareas repetitivas se ejecuten solas, liberando tiempo para actividades de mayor valor.</p>
<h4>¿Qué se puede automatizar?</h4>
<ul>
  <li>📧 Respuesta automática de correos según categoría</li>
  <li>📅 Agendamiento inteligente de reuniones</li>
  <li>📄 Generación de reportes periódicos</li>
  <li>🔔 Alertas basadas en condiciones de datos</li>
  <li>📱 Publicación en redes sociales programada</li>
</ul>
<h4>N8N — La plataforma de automatización visual</h4>
<p><strong>N8N</strong> es una herramienta open-source que permite conectar apps y crear flujos automáticos sin escribir código. Tiene más de 400 integraciones (Gmail, Slack, Google Sheets, ChatGPT, etc.).</p>
<h4>Ejemplo de flujo N8N</h4>
<p>Trigger: nuevo email → Analizar con IA → Si es urgente → Enviar alerta Slack → Crear tarea en Notion → Responder al remitente.</p>',
'https://www.youtube.com/embed/3S5Q3XMksTc',
'N8N: Automatización con IA sin código',16),

(7,
'<h3>Diseño de Flujos: Entrada → Proceso → Acción</h3>
<p>Todo agente inteligente sigue un ciclo fundamental: recibe información, la procesa y ejecuta una acción. Entender este ciclo es clave para diseñar automatizaciones efectivas.</p>
<h4>El modelo PEAS</h4>
<ul>
  <li><strong>P</strong>erformance (desempeño): métrica de éxito del agente</li>
  <li><strong>E</strong>nvironment (entorno): dónde opera el agente</li>
  <li><strong>A</strong>ctuators (actuadores): qué acciones puede realizar</li>
  <li><strong>S</strong>ensors (sensores): cómo recibe información</li>
</ul>
<h4>Diagrama de flujo básico</h4>
<div class="lesson-example">
  <strong>Condición:</strong> Si [el correo contiene "urgente" Y es de un cliente VIP]<br>
  <strong>Entonces:</strong> Enviar SMS al gerente + Crear ticket prioritario + Responder en 5 min<br>
  <strong>Si no:</strong> Clasificar y responder en 24h
</div>
<h4>Herramientas de diseño de flujos</h4>
<p>LangChain permite crear cadenas de razonamiento complejas. Combina: recuperación de información (RAG) + razonamiento del LLM + ejecución de herramientas externas.</p>',
'https://www.youtube.com/embed/aywZrzNaKjs',
'LangChain: Flujos de IA paso a paso',18),

(8,
'<h3>Casos Reales de Agentes de IA en Empresas</h3>
<p>Los agentes de IA ya están transformando industrias. Conocer casos reales te ayuda a identificar oportunidades en tu propio contexto.</p>
<h4>Soporte al cliente autónomo</h4>
<p>Empresas como Zendesk y Intercom usan agentes que resuelven el 70% de consultas sin intervención humana. El agente lee el historial del cliente, consulta la base de conocimiento y genera respuestas personalizadas.</p>
<h4>Ventas inteligentes</h4>
<p>Agentes de IA califican leads automáticamente, personalizan propuestas comerciales y hacen seguimiento por email sin intervención humana.</p>
<h4>Productividad personal</h4>
<p>Herramientas como Notion AI, Microsoft Copilot y Google Duet AI actúan como asistentes que organizan, resumen y generan contenido en tu entorno de trabajo.</p>
<div class="lesson-tip">💡 El futuro del trabajo no es "humano VS máquina" sino "humano CON máquina". Quien domine los agentes de IA tendrá una ventaja competitiva enorme.</div>',
'https://www.youtube.com/embed/9TZLj5PFv7Y',
'Agentes IA en empresas reales: casos de éxito',14);

-- ─── CURSO 3: Visión y NLP (modules 9-12) ───
INSERT IGNORE INTO lesson_content (module_id, contenido_html, video_url, video_titulo, duracion_minutos) VALUES
(9,
'<h3>Visión por Computador: Cómo la IA "Ve"</h3>
<p>La <strong>Visión por Computador</strong> permite a las máquinas interpretar imágenes y videos de manera similar a como lo hace el ojo humano.</p>
<h4>¿Cómo funciona?</h4>
<p>Una imagen es una matriz de píxeles. Las redes neuronales convolucionales (CNN) aprenden a detectar patrones: bordes → formas → objetos → escenas.</p>
<h4>Tareas principales</h4>
<ul>
  <li><strong>Clasificación:</strong> ¿Qué hay en esta imagen? (gato, perro, auto)</li>
  <li><strong>Detección de objetos:</strong> ¿Dónde están y qué son? (bounding boxes)</li>
  <li><strong>Segmentación:</strong> delimita cada objeto píxel a píxel</li>
  <li><strong>Reconocimiento facial:</strong> identifica personas específicas</li>
  <li><strong>OCR:</strong> extrae texto de imágenes</li>
</ul>
<h4>Aplicaciones cotidianas</h4>
<p>Desbloqueo facial del celular, filtros de Instagram, autos autónomos, diagnóstico médico por imágenes, control de calidad industrial.</p>',
'https://www.youtube.com/embed/qRKzBe3JGGs',
'Visión por Computador explicada desde cero',16),

(10,
'<h3>Procesamiento de Lenguaje Natural (NLP)</h3>
<p>El <strong>NLP</strong> (Natural Language Processing) permite a las máquinas entender, interpretar y generar lenguaje humano.</p>
<h4>Tareas fundamentales de NLP</h4>
<ul>
  <li><strong>Tokenización:</strong> dividir texto en unidades mínimas</li>
  <li><strong>Análisis de sentimientos:</strong> detectar emociones en texto</li>
  <li><strong>Traducción automática:</strong> Google Translate, DeepL</li>
  <li><strong>Extracción de entidades:</strong> identificar nombres, lugares, fechas</li>
  <li><strong>Resumen automático:</strong> condensar documentos largos</li>
  <li><strong>Clasificación de texto:</strong> spam, categorías, temas</li>
</ul>
<h4>Transformers: la revolución del NLP</h4>
<p>La arquitectura <strong>Transformer</strong> (2017) revolucionó el NLP con el mecanismo de "atención": permite al modelo entender el contexto de cada palabra en relación con todas las demás. BERT, GPT, T5 y RoBERTa son todos basados en Transformers.</p>',
'https://www.youtube.com/embed/CMrHM8a3hqw',
'NLP: Procesamiento de Lenguaje Natural explicado',18),

(11,
'<h3>Aplicaciones Cotidianas de Visión y NLP</h3>
<p>La IA visual y lingüística ya está integrada en tu día a día, muchas veces sin que lo notes.</p>
<h4>Reconocimiento facial</h4>
<p>Desde desbloquear tu teléfono hasta sistemas de seguridad bancaria. Funciona con redes neuronales que mapean puntos clave del rostro (landmarks) y crean una huella facial única.</p>
<h4>Chatbots de atención al cliente</h4>
<p>Los chatbots modernos usan NLP para entender intención (no solo palabras clave) y mantener contexto en conversaciones largas. Ej: el bot de tu banco que entiende "quiero ver mis movimientos de la semana pasada".</p>
<h4>Filtros de spam</h4>
<p>Gmail clasifica millones de correos por segundo usando modelos de clasificación de texto entrenados con ejemplos de spam vs. legítimo.</p>
<h4>Subtítulos automáticos</h4>
<p>YouTube genera subtítulos en tiempo real combinando ASR (reconocimiento de voz) y NLP para puntuación y formato automático.</p>',
'https://www.youtube.com/embed/aircAruvnKk',
'IA en tu vida: reconocimiento facial y chatbots',12),

(12,
'<h3>Práctica con Google Vision API y Hugging Face</h3>
<p>Hora de pasar a la acción. Existen herramientas accesibles para experimentar con visión e NLP sin necesidad de ser programador experto.</p>
<h4>Google Cloud Vision API</h4>
<p>Permite analizar imágenes para detectar objetos, texto, caras, logotipos y más. Se puede probar directamente en el navegador en cloud.google.com/vision.</p>
<h4>Hugging Face</h4>
<p><strong>Hugging Face</strong> es el "GitHub de la IA". Ofrece miles de modelos preentrenados que se pueden probar gratis en su web (Spaces) sin instalar nada:</p>
<ul>
  <li>Análisis de sentimientos en español</li>
  <li>Traducción entre 200 idiomas</li>
  <li>Generación de resúmenes</li>
  <li>Descripción automática de imágenes</li>
</ul>
<h4>OpenCV Básico</h4>
<p>La biblioteca más popular para visión por computador. En Python: <code>import cv2</code> permite cargar imágenes, aplicar filtros, detectar bordes y analizar video en tiempo real.</p>
<div class="lesson-tip">💡 Prueba ahora: ve a huggingface.co/spaces y busca "sentiment analysis español" para analizar texto en tiempo real.</div>',
'https://www.youtube.com/embed/oVzRxgSoNjQ',
'Hugging Face y Google Vision: primeros pasos',20);
