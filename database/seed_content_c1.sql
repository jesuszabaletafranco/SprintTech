-- ============================================================
-- SprintTech — Seed: Contenido de módulos (lesson_content)
-- Videos YouTube educativos en español por módulo
-- ============================================================
USE sprinttech;

-- ─── CURSO 1: IA Generativa (modules 1-4) ───
INSERT IGNORE INTO lesson_content (module_id, contenido_html, video_url, video_titulo, duracion_minutos) VALUES
(1,
'<h3>¿Qué es la Inteligencia Artificial Generativa?</h3>
<p>La <strong>IA Generativa</strong> es una rama de la inteligencia artificial capaz de crear contenido nuevo: textos, imágenes, código, música y video. A diferencia de la IA tradicional que clasifica o predice, la IA generativa <em>produce</em>.</p>
<h4>Modelos de Lenguaje Grande (LLMs)</h4>
<p>Los <strong>LLMs</strong> (Large Language Models) como GPT-4, Claude y Gemini son entrenados con enormes cantidades de texto. Aprenden patrones del lenguaje humano y pueden generar respuestas coherentes, creativas y precisas.</p>
<ul>
  <li><strong>Parámetros:</strong> son los "ajustes internos" del modelo. GPT-4 tiene ~1.8 billones de parámetros.</li>
  <li><strong>Tokens:</strong> unidades básicas de texto que el modelo procesa (~0.75 palabras por token).</li>
  <li><strong>Temperatura:</strong> controla cuán creativa o predecible es la respuesta (0=determinista, 1=creativo).</li>
</ul>
<h4>¿Cómo funciona internamente?</h4>
<p>El proceso básico es: <code>Entrada (prompt) → Tokenización → Transformación → Predicción → Salida</code>. El modelo predice cuál es el siguiente token más probable, repitiendo el proceso hasta completar la respuesta.</p>
<div class="lesson-tip">💡 <strong>Dato clave:</strong> Herramientas como ChatGPT, Claude, Gemini y Llama son todas basadas en LLMs pero con distintos enfoques, datos de entrenamiento y políticas de uso.</div>',
'https://www.youtube.com/embed/RzkD_rTEBYs',
'¿Qué es la IA Generativa? Explicación completa',
15),

(2,
'<h3>Prompt Engineering: El arte de comunicarse con la IA</h3>
<p>Un <strong>prompt</strong> es la instrucción que le damos a la IA. La calidad del resultado depende directamente de la calidad del prompt. Esta disciplina se llama <em>Prompt Engineering</em>.</p>
<h4>Técnicas fundamentales</h4>
<ul>
  <li><strong>Zero-shot:</strong> pides sin dar ejemplos. "Resume este texto en 3 puntos."</li>
  <li><strong>Few-shot:</strong> das 1-3 ejemplos antes de tu pregunta real.</li>
  <li><strong>Chain of Thought:</strong> pides que "piense paso a paso" para razonar mejor.</li>
  <li><strong>Role prompting:</strong> "Actúa como un experto en marketing y..."</li>
</ul>
<h4>Estructura de un buen prompt</h4>
<p>Un prompt efectivo tiene: <strong>Contexto</strong> (quién eres, qué necesitas), <strong>Tarea</strong> (qué debe hacer), <strong>Formato</strong> (cómo quieres la respuesta) y <strong>Restricciones</strong> (qué evitar).</p>
<div class="lesson-example">
  <strong>❌ Prompt débil:</strong> "Escribe sobre marketing"<br>
  <strong>✅ Prompt fuerte:</strong> "Actúa como un consultor de marketing digital. Escribe 5 estrategias para aumentar ventas en Instagram para una tienda de ropa deportiva en Colombia. Formato: lista numerada con título en negrita y 2 líneas de explicación. Tono: profesional pero cercano."
</div>',
'https://www.youtube.com/embed/1bUy-1hGZpI',
'Prompt Engineering: Cómo hablar con la IA',
18),

(3,
'<h3>Aplicaciones Prácticas de la IA Generativa</h3>
<p>Las herramientas de IA generativa ya transforman el trabajo cotidiano en múltiples áreas. Conocer sus capacidades te permite ahorrar horas y producir más valor.</p>
<h4>ChatGPT — El más versátil</h4>
<p>Ideal para: redacción, resumen, código, análisis, brainstorming, corrección de textos, traducciones y conversaciones complejas.</p>
<h4>Claude — El más preciso en textos largos</h4>
<p>Sobresale en: análisis de documentos extensos, escritura larga y coherente, tareas que requieren razonamiento ético.</p>
<h4>Gemini — Integrado con Google</h4>
<p>Conectado a búsqueda en tiempo real, Google Docs, Gmail y YouTube. Ideal para investigación actualizada.</p>
<h4>Llama — Open Source</h4>
<p>Modelo de Meta que puedes correr <em>localmente</em> sin enviar datos a la nube. Ideal para privacidad y personalización.</p>
<h4>Casos de uso reales</h4>
<ul>
  <li>📄 Generación de contenido y copywriting</li>
  <li>💻 Asistente de código (debugging, explicación, optimización)</li>
  <li>🎨 Creación de imágenes (DALL-E, Midjourney, Stable Diffusion)</li>
  <li>📊 Análisis de datos y generación de reportes</li>
  <li>🎓 Tutor personal y explicación de conceptos</li>
</ul>',
'https://www.youtube.com/embed/hfIUstzHs9A',
'ChatGPT, Claude y Gemini: Diferencias y usos',
20),

(4,
'<h3>Uso Responsable de la IA Generativa</h3>
<p>Con gran poder viene gran responsabilidad. El uso ético de la IA es fundamental para aprovechar sus beneficios sin causar daño.</p>
<h4>Principales riesgos</h4>
<ul>
  <li><strong>Alucinaciones:</strong> la IA puede "inventar" hechos con gran confianza. Siempre verifica datos críticos.</li>
  <li><strong>Sesgos:</strong> los modelos aprenden sesgos de sus datos de entrenamiento. Cuidado con contenido discriminatorio.</li>
  <li><strong>Privacidad:</strong> no compartas datos personales, contraseñas ni información confidencial con IA pública.</li>
  <li><strong>Derechos de autor:</strong> el contenido generado puede tener implicaciones legales. Revisa las políticas de cada herramienta.</li>
</ul>
<h4>Buenas prácticas</h4>
<ul>
  <li>✅ Siempre revisa y valida la información generada</li>
  <li>✅ Indica cuándo usaste IA para crear contenido</li>
  <li>✅ Usa IA para potenciar tu trabajo, no para reemplazar el pensamiento crítico</li>
  <li>✅ Conoce los términos de uso de cada herramienta</li>
</ul>
<div class="lesson-tip">💡 La IA es una herramienta. Quien decide, lidera y asume responsabilidad eres tú.</div>',
'https://www.youtube.com/embed/keMK7-8bvEM',
'Ética y responsabilidad en el uso de IA',
12);
