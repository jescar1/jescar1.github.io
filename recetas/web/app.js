document.addEventListener('DOMContentLoaded', () => {
    // Animación de aparición para las tarjetas de funcionalidades
    const cards = document.querySelectorAll('.feature-card');
    const options = {
        root: null, // viewport
        rootMargin: '0px',
        threshold: 0.2 // Porcentaje de visibilidad para disparar la animación
    };

    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Aplica la clase 'is-visible' con un pequeño retraso para un efecto escalonado
                const index = Array.from(cards).indexOf(entry.target);
                setTimeout(() => {
                    entry.target.classList.add('is-visible');
                }, index * 150); // Retraso de 150ms entre cada tarjeta
                observer.unobserve(entry.target); // Deja de observar una vez animado
            }
        });
    }, options);

    cards.forEach(card => {
        observer.observe(card);
    });

    // Control del header al hacer scroll
    const header = document.querySelector('.header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) { // Si el scroll es mayor a 50px
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
});