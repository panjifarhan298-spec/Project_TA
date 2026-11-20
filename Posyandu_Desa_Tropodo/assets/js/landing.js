
const posyanduList = [
    { id: 1, nama: "Posyandu 1" },
    { id: 2, nama: "Posyandu 2" },
    { id: 3, nama: "Posyandu 3" },
    { id: 4, nama: "Posyandu 4" },
    { id: 5, nama: "Posyandu 5" },
    { id: 6, nama: "Posyandu 6" },
    { id: 7, nama: "Posyandu 7" },
    { id: 8, nama: "Posyandu 8" }
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

        <div class="card posyandu-landing-card h-100 text-center"
             role="button"
             tabindex="0"
             data-id="${item.id}">

            <div class="card-body">

                <div class="posyandu-icon mb-3">
                    <i class="fa-solid ${icons[index]}"></i>
                </div>

                <h6 class="fw-bold">
                    ${item.nama}
                </h6>

                <p class="small text-muted">
                    Desa Tropodo
                </p>

                <p class="small text-primary mb-0">
                    <i class="fa-solid fa-chart-simple"></i>
                    Lihat Detail
                </p>

            </div>
        </div>

    </div>
`).join("");

function tampilkanDetail(id) {
    const data = posyanduList.find(item => item.id === Number(id));

    if (!data) return;

    document.getElementById("modalTitle").textContent = data.nama;

    document.getElementById("modalAddress").textContent =
        "Informasi alamat akan ditambahkan setelah data diverifikasi.";

    const modal = new bootstrap.Modal(
        document.getElementById("posyanduModal")
    );

    modal.show();
}

container.addEventListener("click", function(event) {
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
