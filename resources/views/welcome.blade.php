<x-landing>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          {{ __('ekiliConvo') }}
      </h2>
  </x-slot>

  @include("insights.hero")
  <div class="particles">
    <canvas id="particle-canvas"></canvas>
  </div>
  


<script>
  // Particle effect
function particleEffect() {
    const canvas = document.getElementById('particle-canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    const particlesArray = [];

    const createParticle = () => {
      particlesArray.push({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        size: Math.random() * 3 + 1,
        speedX: (Math.random() * 2) - 1,
        speedY: (Math.random() * 2) - 1
      });
    };

    const drawParticle = (particle) => {
      ctx.fillStyle = 'rgba(255, 255, 255, 0.8)';
      ctx.beginPath();
      ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
      ctx.closePath();
      ctx.fill();
    };

    const updateParticle = (particle) => {
      particle.x += particle.speedX;
      particle.y += particle.speedY;
      if (particle.size > 0.2) particle.size -= 0.01;

      if (particle.x < 0 || particle.x > canvas.width || particle.y < 0 || particle.y > canvas.height) {
        particle.x = Math.random() * canvas.width;
        particle.y = Math.random() * canvas.height;
        particle.size = Math.random() * 3 + 1;
      }
    };

    const animateParticles = () => {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      particlesArray.forEach((particle, index) => {
        updateParticle(particle);
        drawParticle(particle);
      });
      requestAnimationFrame(animateParticles);
    };

    for (let i = 0; i < 100; i++) {
      createParticle();
    }

    animateParticles();
  }

  window.addEventListener('resize', () => {
    const canvas = document.getElementById('particle-canvas');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
  });

  particleEffect();
</script>

</x-landing>
