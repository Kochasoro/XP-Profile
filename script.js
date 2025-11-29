// Scroll-based nav highlight
const sections = document.querySelectorAll("section");
const navLinks = document.querySelectorAll(".nav-link");

window.addEventListener("scroll", () => {
  let current = "";

  sections.forEach(section => {
    const sectionTop = section.offsetTop;
    const sectionHeight = section.clientHeight;
    if (pageYOffset >= sectionTop - sectionHeight / 3) {
      current = section.getAttribute("id");
    }
  });

  navLinks.forEach(link => {
    link.classList.remove("active");
    if (link.getAttribute("href").includes(current)) {
      link.classList.add("active");
    }
  });
});
// Project carousel functionality
const track = document.querySelector('.carousel-track');
const nextBtn = document.querySelector('.carousel-btn.next');
const prevBtn = document.querySelector('.carousel-btn.prev');






document.addEventListener('DOMContentLoaded', () => {
  const track = document.querySelector('.carousel-track');
  const prevBtn = document.querySelector('.carousel-btn.prev');
  const nextBtn = document.querySelector('.carousel-btn.next');
  const items = document.querySelectorAll('.carousel-item');

  if (!track || !prevBtn || !nextBtn || items.length === 0) return;

  let currentIndex = 0;

  const scrollToItem = (index) => {
    if (index < 0 || index >= items.length) return;
    items[index].scrollIntoView({ behavior: 'smooth', inline: 'center' });
    currentIndex = index;
  };

  prevBtn.addEventListener('click', () => {
    scrollToItem(currentIndex - 1);
  });

  nextBtn.addEventListener('click', () => {
    scrollToItem(currentIndex + 1);
  });

  const updateCarouselButtons = () => {
    prevBtn.style.display = currentIndex > 0 ? 'block' : 'none';
    nextBtn.style.display = currentIndex < items.length - 1 ? 'block' : 'none';
  };

  // Ensure buttons update on scroll end
  track.addEventListener('scroll', () => {
    const scrollLeft = track.scrollLeft + track.offsetWidth / 2;
    currentIndex = [...items].findIndex(item => item.offsetLeft + item.offsetWidth / 2 > scrollLeft - 1);
    updateCarouselButtons();
  });

  window.addEventListener('load', () => {
    scrollToItem(0);
    updateCarouselButtons();
  });

  window.addEventListener('resize', () => {
    scrollToItem(currentIndex);
  });
});
  // Dark Mode Toggle
const darkModeToggle = document.getElementById("darkModeToggle");

darkModeToggle.addEventListener("click", () => {
  document.body.classList.toggle("dark-mode");

  // Change button text (optional)
  if (document.body.classList.contains("dark-mode")) {
    darkModeToggle.textContent = "🌞"; // Switch to light mode
  } else {
    darkModeToggle.textContent = "🌙"; // Switch to dark mode
  }
});





