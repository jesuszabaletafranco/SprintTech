/**
 * SprintTech LearningWithIA — Main JavaScript
 * Handles: Navbar, Particles, Scroll animations, Dropdowns, Modals
 */

document.addEventListener('DOMContentLoaded', function () {

  // ──────────────────────────────────────────
  // 1. NAVBAR — scroll effect & hamburger
  // ──────────────────────────────────────────
  const navbar       = document.getElementById('navbar');
  const hamburger    = document.getElementById('navHamburger');
  const navMenu      = document.getElementById('navMenu');
  const userMenuBtn  = document.getElementById('userMenuBtn');
  const userDropdown = document.getElementById('userDropdown');

  // Navbar scroll
  if (navbar) {
    window.addEventListener('scroll', () => {
      navbar.classList.toggle('scrolled', window.scrollY > 20);
    });
  }

  // Hamburger toggle
  if (hamburger && navMenu) {
    hamburger.addEventListener('click', () => {
      const isOpen = navMenu.classList.toggle('open');
      hamburger.classList.toggle('open', isOpen);
      hamburger.setAttribute('aria-label', isOpen ? 'Cerrar menú' : 'Abrir menú');
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
      if (!navbar.contains(e.target)) {
        navMenu.classList.remove('open');
        hamburger.classList.remove('open');
      }
    });
  }

  // User dropdown
  if (userMenuBtn && userDropdown) {
    userMenuBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const isOpen = userDropdown.classList.toggle('open');
      userMenuBtn.setAttribute('aria-expanded', isOpen);
    });

    document.addEventListener('click', (e) => {
      if (!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) {
        userDropdown.classList.remove('open');
        userMenuBtn.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // ──────────────────────────────────────────
  // 2. HERO PARTICLES (Canvas)
  // ──────────────────────────────────────────
  const canvas = document.getElementById('particleCanvas');
  if (canvas) {
    const ctx = canvas.getContext('2d');
    let particles = [];
    const PARTICLE_COUNT = 70;
    const COLORS = ['rgba(0,212,255,0.6)', 'rgba(124,58,237,0.6)', 'rgba(0,212,255,0.3)', 'rgba(255,255,255,0.3)'];

    function resizeCanvas() {
      canvas.width  = canvas.offsetWidth;
      canvas.height = canvas.offsetHeight;
    }

    class Particle {
      constructor() { this.reset(); }
      reset() {
        this.x    = Math.random() * canvas.width;
        this.y    = Math.random() * canvas.height;
        this.r    = Math.random() * 2 + 0.5;
        this.vx   = (Math.random() - 0.5) * 0.4;
        this.vy   = (Math.random() - 0.5) * 0.4;
        this.life = Math.random();
        this.maxLife = Math.random() * 0.6 + 0.4;
        this.color = COLORS[Math.floor(Math.random() * COLORS.length)];
      }
      update() {
        this.x += this.vx;
        this.y += this.vy;
        this.life -= 0.002;
        if (this.life <= 0) this.reset();
        if (this.x < 0 || this.x > canvas.width)  this.vx *= -1;
        if (this.y < 0 || this.y > canvas.height)  this.vy *= -1;
      }
      draw() {
        ctx.save();
        ctx.globalAlpha = this.life;
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.r, 0, Math.PI * 2);
        ctx.fillStyle = this.color;
        ctx.fill();
        ctx.restore();
      }
    }

    function initParticles() {
      particles = [];
      for (let i = 0; i < PARTICLE_COUNT; i++) {
        particles.push(new Particle());
      }
    }

    function drawLines() {
      for (let i = 0; i < particles.length; i++) {
        for (let j = i + 1; j < particles.length; j++) {
          const dx   = particles[i].x - particles[j].x;
          const dy   = particles[i].y - particles[j].y;
          const dist = Math.sqrt(dx * dx + dy * dy);
          if (dist < 100) {
            ctx.save();
            ctx.globalAlpha = (1 - dist / 100) * 0.12;
            ctx.strokeStyle = 'rgba(0,212,255,1)';
            ctx.lineWidth   = 0.5;
            ctx.beginPath();
            ctx.moveTo(particles[i].x, particles[i].y);
            ctx.lineTo(particles[j].x, particles[j].y);
            ctx.stroke();
            ctx.restore();
          }
        }
      }
    }

    function animate() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      particles.forEach(p => { p.update(); p.draw(); });
      drawLines();
      requestAnimationFrame(animate);
    }

    resizeCanvas();
    initParticles();
    animate();

    let resizeTimeout;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimeout);
      resizeTimeout = setTimeout(() => { resizeCanvas(); initParticles(); }, 200);
    });
  }

  // ──────────────────────────────────────────
  // 3. SCROLL FADE-IN ANIMATIONS
  // ──────────────────────────────────────────
  const fadeElements = document.querySelectorAll('.fade-in');
  if (fadeElements.length > 0) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    fadeElements.forEach(el => observer.observe(el));
  }

  // ──────────────────────────────────────────
  // 4. PROGRESS BARS (animate on view)
  // ──────────────────────────────────────────
  const progressBars = document.querySelectorAll('.progress-bar[data-width]');
  if (progressBars.length > 0) {
    const progObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const bar = entry.target;
          bar.style.width = bar.dataset.width + '%';
          progObserver.unobserve(bar);
        }
      });
    }, { threshold: 0.3 });

    progressBars.forEach(bar => {
      bar.style.width = '0%';
      progObserver.observe(bar);
    });
  }

  // ──────────────────────────────────────────
  // 5. FLASH MESSAGE AUTO-DISMISS
  // ──────────────────────────────────────────
  const flashes = document.querySelectorAll('.flash');
  flashes.forEach(flash => {
    setTimeout(() => {
      flash.style.transition = 'opacity 0.4s ease';
      flash.style.opacity = '0';
      setTimeout(() => flash.remove(), 400);
    }, 5000);
  });

  // ──────────────────────────────────────────
  // 6. MODAL SYSTEM
  // ──────────────────────────────────────────
  window.openModal = function(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.add('open');
  };

  window.closeModal = function(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.remove('open');
  };

  // Close modal on overlay click
  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
      if (e.target === this) {
        this.classList.remove('open');
      }
    });
  });

  // Escape key closes modal
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-overlay.open').forEach(m => m.classList.remove('open'));
    }
  });

  // ──────────────────────────────────────────
  // 7. STATS COUNTER ANIMATION
  // ──────────────────────────────────────────
  const statValues = document.querySelectorAll('.stat-value[data-target]');
  if (statValues.length > 0) {
    const countObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        const el      = entry.target;
        const target  = parseInt(el.dataset.target, 10);
        const suffix  = el.dataset.suffix || '';
        const duration = 1500;
        const start    = Date.now();

        function tick() {
          const progress = Math.min((Date.now() - start) / duration, 1);
          const eased    = 1 - Math.pow(1 - progress, 3); // ease-out cubic
          el.textContent = Math.round(eased * target) + suffix;
          if (progress < 1) requestAnimationFrame(tick);
        }
        tick();
        countObserver.unobserve(el);
      });
    }, { threshold: 0.5 });

    statValues.forEach(el => countObserver.observe(el));
  }

  // ──────────────────────────────────────────
  // 8. PASSWORD STRENGTH METER
  // ──────────────────────────────────────────
  const passInput  = document.getElementById('password');
  const strengthEl = document.getElementById('passwordStrength');

  if (passInput && strengthEl) {
    const bars = strengthEl.querySelectorAll('.strength-bar');

    passInput.addEventListener('input', () => {
      const val = passInput.value;
      let score = 0;
      if (val.length >= 8) score++;
      if (/[A-Z]/.test(val)) score++;
      if (/[0-9]/.test(val)) score++;
      if (/[^A-Za-z0-9]/.test(val)) score++;

      bars.forEach((bar, i) => {
        bar.className = 'strength-bar';
        if (i < score) {
          if (score <= 1) bar.classList.add('weak');
          else if (score <= 2) bar.classList.add('medium');
          else  bar.classList.add('strong');
        }
      });
    });
  }

  // ──────────────────────────────────────────
  // 9. PASSWORD VISIBILITY TOGGLE
  // ──────────────────────────────────────────
  document.querySelectorAll('.input-toggle[data-target]').forEach(btn => {
    btn.addEventListener('click', () => {
      const targetId = btn.dataset.target;
      const input    = document.getElementById(targetId);
      if (!input) return;

      if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
      } else {
        input.type = 'password';
        btn.innerHTML = '<i class="fa-solid fa-eye"></i>';
      }
    });
  });

  // ──────────────────────────────────────────
  // 10. COPY CERT CODE
  // ──────────────────────────────────────────
  document.querySelectorAll('.btn-copy-cert').forEach(btn => {
    btn.addEventListener('click', () => {
      const code = btn.dataset.code;
      if (!code) return;
      navigator.clipboard.writeText(code).then(() => {
        const original = btn.innerHTML;
        btn.innerHTML  = '<i class="fa-solid fa-check"></i>';
        btn.style.color = 'var(--clr-green)';
        setTimeout(() => {
          btn.innerHTML  = original;
          btn.style.color = '';
        }, 2000);
      });
    });
  });

});
