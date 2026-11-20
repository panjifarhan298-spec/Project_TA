
const posyanduList = [
    { id: 1, nama: "Posyandu Tropodo 1", alamat: "Dusun Tropodo 1, Desa Tropodo" },
    { id: 2, nama: "Posyandu Tropodo 2", alamat: "Dusun Tropodo 2, Desa Tropodo" },
    { id: 3, nama: "Posyandu Tropodo 3", alamat: "Dusun Tropodo 3, Desa Tropodo" },
    { id: 4, nama: "Posyandu Tropodo 4", alamat: "Dusun Tropodo 4, Desa Tropodo" },
    { id: 5, nama: "Posyandu Tropodo 5", alamat: "Dusun Tropodo 5, Desa Tropodo" },
    { id: 6, nama: "Posyandu Tropodo 6", alamat: "Dusun Tropodo 6, Desa Tropodo" },
    { id: 7, nama: "Posyandu Tropodo 7", alamat: "Dusun Tropodo 7, Desa Tropodo" },
    { id: 8, nama: "Posyandu Tropodo 8", alamat: "Dusun Tropodo 8, Desa Tropodo" }
];

const icons = [
    "fa-house-medical",
    "fa-hand-holding-medical",
    "fa-baby",
    "fa-heart-pulse",
    "fa-syringe",
    "fa-weight-scale",
    "fa-ruler-vertical",
    "fa-child"
];

const container = document.getElementById("posyanduCards");

container.innerHTML = posyanduList.map((item, index) => `
    <div class="col-6 col-md-4 col-lg-3">

        <div class="card posyandu-landing-card posyandu-clickable h-100 text-center"
             role="button"
             tabindex="0"
             data-id="${item.id}">

            <div class="card-body">

                <div class="posyandu-icon mb-3">
                    <i class="fa-solid ${icons[index]}"></i>
                </div>

                <h6 class="fw-bold mb-1" style="color:#123B70;">
                    ${item.nama}
                </h6>

                <p class="small text-muted mb-2">
                    ${item.alamat}
                </p>

                <div class="posyandu-category-counts" aria-label="Kategori layanan">
                    <span class="badge-status badge-normal"><i class="fa-solid fa-child-reaching me-1"></i>Balita</span>
                    <span class="badge-status badge-ibu-hamil"><i class="fa-solid fa-person-pregnant me-1"></i>Bumil</span>
                    <span class="badge-status badge-lansia"><i class="fa-solid fa-person-cane me-1"></i>Lansia</span>
                </div>

                <div class="posyandu-data-links mt-2">
                    <a href="#batasan" class="btn btn-data-link btn-data-balita" onclick="event.stopPropagation()">Balita</a>
                    <a href="#batasan" class="btn btn-data-link btn-data-ibu" onclick="event.stopPropagation()">Bumil</a>
                    <a href="#batasan" class="btn btn-data-link btn-data-lansia" onclick="event.stopPropagation()">Lansia</a>
                </div>

                <p class="small mt-2 mb-0 fw-semibold" style="color:#60A5FA;">
                    <i class="fa-solid fa-chart-simple me-1"></i>Lihat Detail
                </p>

            </div>
        </div>

    </div>
`).join("");

function tampilkanDetail(id) {
    const data = posyanduList.find(item => item.id === Number(id));

    if (!data) return;

    document.getElementById("modalTitle").textContent = data.nama;

    document.getElementById("modalAddress").textContent = data.alamat;

    const modal = new bootstrap.Modal(
        document.getElementById("posyanduModal")
    );

    modal.show();
}

container.addEventListener("click", function(event) {
    if (event.target.closest("a")) return;
    const card = event.target.closest("[data-id]");

    if (card) {
        tampilkanDetail(card.dataset.id);
    }
});

container.addEventListener("keydown", function(event) {
    if (
        (event.key === "Enter" || event.key === " ") &&
        event.target.matches("[data-id]")
    ) {
        event.preventDefault();
        tampilkanDetail(event.target.dataset.id);
    }
});

document.getElementById("currentYear").textContent =
    new Date().getFullYear();

const splash = document.getElementById("heroSplashScreen");
if (splash) {
    window.setTimeout(function() {
        splash.classList.add("is-hidden");
        window.setTimeout(function() {
            splash.remove();
        }, 700);
    }, 2200);
}
