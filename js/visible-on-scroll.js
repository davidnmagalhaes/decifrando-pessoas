const elementsToAnimate = document.querySelectorAll('.animate-on-scroll');

const options = {
  root: null, // Use o viewport como o elemento de referência
  rootMargin: '0px', // Margem ao redor do viewport
  threshold: 0.5, // Quando pelo menos 50% do elemento estiver visível
};

const observer = new IntersectionObserver((entries, observer) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.classList.add('active'); // Adiciona a classe 'active' quando o elemento está visível
      observer.unobserve(entry.target); // Deixa de observar após a animação ter sido ativada
    }
  });
}, options);

elementsToAnimate.forEach((element) => {
  observer.observe(element); // Começa a observar os elementos
});
