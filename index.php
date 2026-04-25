<?php
// ============================================================
// index.php — Landing page / Página principal
// ============================================================
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/database.php';

$loggedIn = isLoggedIn();
$db       = getDB();

// Obtener los primeros 3 cursos para preview
$courses = $db->query('SELECT * FROM courses WHERE activo = 1 ORDER BY orden LIMIT 3')->fetchAll();

$pageTitle = 'SprintTech — Aprende. Compite. Gana. | Plataforma de Aprendizaje Interactivo';
require_once __DIR__ . '/includes/header.php';
?>

<!-- ═══════════════════ HERO ═══════════════════ -->
<section class="hero" id="hero">
  <canvas class="hero-canvas" id="particleCanvas"></canvas>
  <div class="hero-glow-1"></div>
  <div class="hero-glow-2"></div>

  <div class="container" style="padding-top: 24px; padding-bottom: 80px;">
    <div class="hero-content">
      <!-- Badge -->
      <div class="hero-logo-badge fade-in">
        <i class="fa-solid fa-bolt-lightning"></i>
        LearningWithIA — Tecnologías Emergentes
      </div>

      <!-- Título principal -->
      <h1 class="hero-title fade-in fade-in-delay-1">
        Sprint Tech —
        <span class="line-accent">Aprende. Compite.</span>
        Gana.
      </h1>

      <!-- Subtítulo -->
      <p class="hero-subtitle fade-in fade-in-delay-2">
        Capacítate en Tecnologías 4.0 de manera fácil y rápida.
        Plataforma de Aprendizaje Interactivo basada en el modelo
        <strong style="color:var(--clr-cyan);">ABR — Aprendizaje Basado en Retos</strong>.
      </p>

      <!-- CTAs -->
      <div class="hero-cta fade-in fade-in-delay-3">
        <?php if ($loggedIn): ?>
          <a href="<?= baseUrl('/user/my_courses.php') ?>" class="btn btn-primary btn-lg" id="btn-hero-mycourses">
            <i class="fa-solid fa-book-open"></i> Mis Cursos
          </a>
          <a href="<?= baseUrl('/courses/index.php') ?>" class="btn btn-ghost btn-lg" id="btn-hero-courses">
            <i class="fa-solid fa-graduation-cap"></i> Ver todos los cursos
          </a>
        <?php else: ?>
          <a href="<?= baseUrl('/auth/register.php') ?>" class="btn btn-primary btn-lg" id="btn-hero-register">
            <i class="fa-solid fa-rocket"></i> Comienza gratis hoy
          </a>
          <a href="<?= baseUrl('/courses/index.php') ?>" class="btn btn-ghost btn-lg" id="btn-hero-courses">
            <i class="fa-solid fa-eye"></i> Explorar cursos
          </a>
        <?php endif; ?>
      </div>

      <!-- Stats -->
      <div class="hero-stats fade-in" style="transition-delay:0.4s;">
        <div class="stat-item">
          <div class="stat-value"><span data-target="6" data-suffix="">0</span></div>
          <div class="stat-label">Cursos disponibles</div>
        </div>
        <div class="stat-item">
          <div class="stat-value"><span data-target="74" data-suffix="h">0h</span></div>
          <div class="stat-label">Horas de contenido</div>
        </div>
        <div class="stat-item">
          <div class="stat-value"><span data-target="100" data-suffix="%">0%</span></div>
          <div class="stat-label">Gratis e interactivo</div>
        </div>
        <div class="stat-item">
          <div class="stat-value"><span data-target="4" data-suffix=".0">0.0</span></div>
          <div class="stat-label">Tecnologías 4.0</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Wave separator -->
  <div style="position:absolute; bottom:0; left:0; right:0; overflow:hidden; line-height:0;">
    <svg viewBox="0 0 1200 80" preserveAspectRatio="none" style="width:100%; height:80px; display:block;">
      <path d="M0,40 C300,80 600,0 900,40 C1050,60 1150,30 1200,40 L1200,80 L0,80 Z"
            fill="rgba(13,18,33,1)"></path>
    </svg>
  </div>
</section>

<!-- ═══════════════════ PUBLICIDAD / FEATURES ═══════════════════ -->
<section class="section" style="padding-top: 100px; background: #0d1221;">
  <div class="container">
    <div class="section-header fade-in">
      <span class="section-label"><i class="fa-solid fa-star"></i> ¿Por qué SprintTech?</span>
      <h2 class="section-title mt-24">
        La plataforma que te prepara para el
        <span class="text-gradient">futuro digital</span>
      </h2>
      <p class="section-subtitle">
        Aprende con tecnología de punta, metodología probada y contenido actualizado por expertos.
      </p>
    </div>

    <div class="features-grid">
      <!-- Feature 1 -->
      <div class="feature-card fade-in" id="feature-abr">
        <div class="feature-icon" style="background: linear-gradient(135deg, rgba(0,212,255,0.2), rgba(0,212,255,0.05)); color: var(--clr-cyan);">
          <i class="fa-solid fa-arrows-spin"></i>
        </div>
        <h3 class="feature-title">Metodología ABR</h3>
        <p class="feature-desc">
          Aprendizaje Basado en Retos: cada curso tiene un proyecto real que debes resolver. Aprendes haciendo, no solo viendo videos.
        </p>
      </div>

      <!-- Feature 2 -->
      <div class="feature-card fade-in fade-in-delay-1" id="feature-ia">
        <div class="feature-icon" style="background: linear-gradient(135deg, rgba(124,58,237,0.2), rgba(124,58,237,0.05)); color: #a78bfa;">
          <i class="fa-solid fa-robot"></i>
        </div>
        <h3 class="feature-title">IA Generativa Real</h3>
        <p class="feature-desc">
          Practica con ChatGPT, Claude, Gemini y Llama. No solo teoría — usas las herramientas líderes del mercado desde el día 1.
        </p>
      </div>

      <!-- Feature 3 -->
      <div class="feature-card fade-in fade-in-delay-2" id="feature-cert">
        <div class="feature-icon" style="background: linear-gradient(135deg, rgba(16,185,129,0.2), rgba(16,185,129,0.05)); color: var(--clr-green);">
          <i class="fa-solid fa-certificate"></i>
        </div>
        <h3 class="feature-title">Certificaciones Digitales</h3>
        <p class="feature-desc">
          Completa los retos y obtén tu certificado digital con código único verificable. Demuestra tus habilidades al mundo.
        </p>
      </div>

      <!-- Feature 4 -->
      <div class="feature-card fade-in" id="feature-path">
        <div class="feature-icon" style="background: linear-gradient(135deg, rgba(245,158,11,0.2), rgba(245,158,11,0.05)); color: var(--clr-amber);">
          <i class="fa-solid fa-route"></i>
        </div>
        <h3 class="feature-title">Ruta de Formación</h3>
        <p class="feature-desc">
          6 cursos organizados en una ruta progresiva desde IA Generativa hasta IoT Industrial. Un camino claro hacia el futuro.
        </p>
      </div>

      <!-- Feature 5 -->
      <div class="feature-card fade-in fade-in-delay-1" id="feature-free">
        <div class="feature-icon" style="background: linear-gradient(135deg, rgba(239,68,68,0.2), rgba(239,68,68,0.05)); color: var(--clr-red);">
          <i class="fa-solid fa-infinity"></i>
        </div>
        <h3 class="feature-title">100% Gratuito</h3>
        <p class="feature-desc">
          Acceso completo a todos los cursos sin costo. Regístrate en segundos y empieza a aprender en Tecnologías 4.0.
        </p>
      </div>

      <!-- Feature 6 -->
      <div class="feature-card fade-in fade-in-delay-2" id="feature-community">
        <div class="feature-icon" style="background: linear-gradient(135deg, rgba(6,182,212,0.2), rgba(6,182,212,0.05)); color: #22d3ee;">
          <i class="fa-solid fa-users"></i>
        </div>
        <h3 class="feature-title">Impacto Real</h3>
        <p class="feature-desc">
          Diseña soluciones para tu comunidad: apps móviles, sistemas IoT, agentes de IA y más con impacto social medible.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════ RUTA DE FORMACIÓN ═══════════════════ -->
<section class="section" style="background: var(--clr-bg);">
  <div class="container">
    <div class="section-header fade-in">
      <span class="section-label"><i class="fa-solid fa-map"></i> Ruta de Formación</span>
      <h2 class="section-title mt-24">
        Tecnologías <span class="text-gradient">Emergentes</span> — Nivel Básico
      </h2>
      <p class="section-subtitle">
        6 cursos en secuencia lógica. Empieza con IA y termina dominando IoT Industrial.
      </p>
    </div>

    <!-- Timeline visual -->
    <div style="position:relative; max-width:800px; margin:0 auto;">
      <!-- Línea vertical -->
      <div style="position:absolute; left:39px; top:0; bottom:0; width:2px; background: linear-gradient(180deg, var(--clr-cyan), var(--clr-violet)); opacity:0.3; border-radius:1px;"></div>

      <?php
      $allCourses = $db->query('SELECT * FROM courses WHERE activo = 1 ORDER BY orden')->fetchAll();
      foreach ($allCourses as $i => $c):
        $delays = ['', 'fade-in-delay-1', 'fade-in-delay-2', '', 'fade-in-delay-1', 'fade-in-delay-2'];
        $delay  = $delays[$i] ?? '';
      ?>
      <div class="fade-in <?= $delay ?>" style="display:flex; gap:20px; padding:0 0 28px 0; align-items:flex-start;">
        <!-- Número -->
        <div style="width:80px; height:80px; border-radius:50%; background: linear-gradient(135deg, <?= htmlspecialchars($c['color_accent']) ?>, #7c3aed); display:flex; align-items:center; justify-content:center; flex-shrink:0; position:relative; z-index:1; box-shadow: 0 0 20px <?= htmlspecialchars($c['color_accent']) ?>44;">
          <i class="fa-solid <?= htmlspecialchars($c['icono']) ?>" style="font-size:1.8rem; color:white;"></i>
        </div>
        <!-- Contenido -->
        <div style="flex:1; background:var(--clr-bg-card); border:1px solid var(--clr-border); border-left:3px solid <?= htmlspecialchars($c['color_accent']) ?>; border-radius:var(--radius-md); padding:20px 24px; margin-top:12px;">
          <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; margin-bottom:8px;">
            <h3 style="font-size:1rem; color:var(--clr-white);">
              Curso <?= $i + 1 ?>: <?= htmlspecialchars($c['nombre']) ?>
            </h3>
            <span class="badge badge-cyan"><i class="fa-regular fa-clock"></i> <?= (int)$c['duracion_horas'] ?>h</span>
          </div>
          <p style="font-size:0.85rem; color:var(--clr-text-muted); line-height:1.6; margin-bottom:12px;">
            <?= htmlspecialchars(mb_substr($c['objetivo'], 0, 100)) ?>...
          </p>
          <?php if (!empty($c['herramientas'])): ?>
          <div style="display:flex; gap:6px; flex-wrap:wrap;">
            <?php foreach (array_slice(explode(',', $c['herramientas']), 0, 3) as $tool): ?>
              <span style="font-size:0.7rem; padding:2px 8px; background:rgba(255,255,255,0.04); border:1px solid var(--clr-border); border-radius:100px; color:var(--clr-text-dim);">
                <?= trim(htmlspecialchars($tool)) ?>
              </span>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div style="text-align:center; margin-top:32px;" class="fade-in">
      <a href="<?= baseUrl('/courses/index.php') ?>" class="btn btn-primary btn-lg" id="btn-view-all-courses">
        <i class="fa-solid fa-graduation-cap"></i> Ver todos los cursos con detalle
      </a>
    </div>
  </div>
</section>

<!-- ═══════════════════ ABR SECTION ═══════════════════ -->
<section class="section" style="background: linear-gradient(135deg, #0d1221 0%, #130d21 100%);">
  <div class="container">
    <div class="section-header fade-in">
      <span class="section-label"><i class="fa-solid fa-circle-nodes"></i> Metodología</span>
      <h2 class="section-title mt-24">
        Ciclo de <span class="text-gradient">Aprendizaje ABR</span>
      </h2>
      <p class="section-subtitle">
        Cada curso sigue el mismo ciclo probado de aprendizaje activo y reflexivo.
      </p>
    </div>

    <div style="display:flex; justify-content:center; gap:0; flex-wrap:wrap; max-width:900px; margin:0 auto; position:relative;">
      <?php
      $abrCycle = [
        ['🎯', 'Problema Real',      'Partís de un reto auténtico del mundo actual', '#00d4ff'],
        ['⚙️', 'Tecnología',        'Usás herramientas digitales reales del mercado', '#7c3aed'],
        ['🔄', 'Prueba y Error',     'Iterás y mejorás tu solución en el proceso', '#06b6d4'],
        ['✅', 'Solución',           'Presentás un producto o propuesta funcional', '#10b981'],
        ['💡', 'Reflexión',          'Consolidás el aprendizaje y proyectás su impacto', '#f59e0b'],
      ];
      foreach ($abrCycle as $idx => $step): ?>
      <div class="fade-in fade-in-delay-<?= $idx % 3 ?>" style="text-align:center; padding:28px 20px; flex:0 0 180px; position:relative;">
        <?php if ($idx < count($abrCycle) - 1): ?>
        <div style="position:absolute; top:50px; right:-20px; color:var(--clr-text-dim); font-size:1.2rem; z-index:1;">
          <i class="fa-solid fa-chevron-right"></i>
        </div>
        <?php endif; ?>
        <div style="width:80px; height:80px; border-radius:50%; background: rgba(255,255,255,0.04); border: 2px solid <?= $step[3] ?>; display:flex; align-items:center; justify-content:center; font-size:2rem; margin:0 auto 16px; box-shadow: 0 0 20px <?= $step[3] ?>33;">
          <?= $step[0] ?>
        </div>
        <h4 style="font-size:0.95rem; font-weight:700; color:var(--clr-white); margin-bottom:8px;"><?= $step[1] ?></h4>
        <p style="font-size:0.78rem; color:var(--clr-text-muted); line-height:1.5;"><?= $step[2] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══════════════════ INDICADORES ═══════════════════ -->
<section class="section" style="background: var(--clr-bg);">
  <div class="container">
    <div class="section-header fade-in">
      <span class="section-label"><i class="fa-solid fa-chart-line"></i> Resultados</span>
      <h2 class="section-title mt-24">
        Indicadores de <span class="text-gradient">Efectividad</span>
      </h2>
    </div>

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:24px; max-width:900px; margin:0 auto;">
      <?php
      $indicators = [
        ['80%', 'Completa los retos', '#00d4ff', 'fa-trophy'],
        ['70%', 'Aplica soluciones reales', '#7c3aed', 'fa-lightbulb'],
        ['+', 'Mejora en resolución de problemas', '#10b981', 'fa-brain'],
        ['✓', 'Uso autónomo de herramientas', '#f59e0b', 'fa-wrench'],
      ];
      foreach ($indicators as $ind): ?>
      <div class="feature-card fade-in" style="text-align:center;">
        <div style="font-size:2.5rem; font-weight:900; font-family:var(--ff-heading); color:<?= $ind[2] ?>; margin-bottom:12px;">
          <?= $ind[0] ?>
        </div>
        <i class="fa-solid <?= $ind[3] ?>" style="font-size:1.5rem; color:<?= $ind[2] ?>; opacity:0.6; margin-bottom:12px; display:block;"></i>
        <p style="font-size:0.9rem; color:var(--clr-text-muted);"><?= $ind[1] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══════════════════ CTA FINAL ═══════════════════ -->
<section class="section" style="background: linear-gradient(135deg, #080c18, #0d1a3a, #1a0d2e);">
  <div class="container" style="text-align:center;">
    <div class="fade-in">
      <span class="section-label"><i class="fa-solid fa-rocket"></i> Empieza hoy</span>
      <h2 style="font-size:clamp(2rem, 4vw, 3.2rem); font-weight:900; margin:24px 0 20px; color:white;">
        Tu futuro en el<br>
        <span class="text-gradient">mundo digital comienza aquí</span>
      </h2>
      <p style="color:var(--clr-text-muted); font-size:1.05rem; margin-bottom:40px; max-width:560px; margin-left:auto; margin-right:auto; margin-bottom:40px;">
        Únete a la plataforma que te enseña a usar IA, crear apps, entender IoT y más. Sin costo. Sin excusas.
      </p>
      <div style="display:flex; gap:16px; justify-content:center; flex-wrap:wrap;">
        <?php if ($loggedIn): ?>
          <a href="<?= baseUrl('/courses/index.php') ?>" class="btn btn-primary btn-lg" id="btn-cta-courses">
            <i class="fa-solid fa-graduation-cap"></i> Explorar cursos
          </a>
        <?php else: ?>
          <a href="<?= baseUrl('/auth/register.php') ?>" class="btn btn-primary btn-lg" id="btn-cta-register">
            <i class="fa-solid fa-user-plus"></i> Crear cuenta gratis
          </a>
          <a href="<?= baseUrl('/courses/index.php') ?>" class="btn btn-ghost btn-lg" id="btn-cta-view-courses">
            <i class="fa-solid fa-graduation-cap"></i> Ver cursos
          </a>
        <?php endif; ?>
      </div>

      <!-- Mini badge de confianza -->
      <div style="display:flex; gap:24px; justify-content:center; margin-top:40px; flex-wrap:wrap;">
        <span style="display:flex; align-items:center; gap:8px; font-size:0.85rem; color:var(--clr-text-muted);">
          <i class="fa-solid fa-shield-halved" style="color:var(--clr-green);"></i> Sin costo
        </span>
        <span style="display:flex; align-items:center; gap:8px; font-size:0.85rem; color:var(--clr-text-muted);">
          <i class="fa-solid fa-certificate" style="color:var(--clr-amber);"></i> Certificado incluido
        </span>
        <span style="display:flex; align-items:center; gap:8px; font-size:0.85rem; color:var(--clr-text-muted);">
          <i class="fa-solid fa-infinity" style="color:var(--clr-cyan);"></i> Acceso ilimitado
        </span>
        <span style="display:flex; align-items:center; gap:8px; font-size:0.85rem; color:var(--clr-text-muted);">
          <i class="fa-solid fa-rocket" style="color:#a78bfa;"></i> Metodología ABR
        </span>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
