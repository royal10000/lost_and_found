const menuBtn = document.getElementById("menuBtn");
const mobileMenu = document.getElementById("mobileMenu");

if (menuBtn && mobileMenu) {
  const closeMenu = () => {
    mobileMenu.classList.add("hidden");
    menuBtn.setAttribute("aria-expanded", "false");
    menuBtn.setAttribute("aria-label", "Open navigation menu");
  };

  menuBtn.addEventListener("click", () => {
    const isOpen = !mobileMenu.classList.contains("hidden");
    mobileMenu.classList.toggle("hidden", isOpen);
    menuBtn.setAttribute("aria-expanded", String(!isOpen));
    menuBtn.setAttribute("aria-label", isOpen ? "Open navigation menu" : "Close navigation menu");
  });

  mobileMenu.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", closeMenu);
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") closeMenu();
  });
}

function togglePassword(inputId, iconId) {
  const input = document.getElementById(inputId);
  const icon = document.getElementById(iconId);

  if (!input || !icon) return;

  if (input.type === "password") {
    input.type = "text";
  } else {
    input.type = "password";
  }
}
