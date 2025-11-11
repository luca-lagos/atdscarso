document.addEventListener("DOMContentLoaded", () => {
    const toggle = document.createElement("button");
    toggle.className =
        "fixed top-4 left-4 z-50 bg-gray-800 text-white p-2 rounded-md";
    toggle.innerHTML = "☰";
    document.body.appendChild(toggle);

    toggle.addEventListener("click", () => {
        document.body.classList.toggle("layout-collapsed");
    });
});
