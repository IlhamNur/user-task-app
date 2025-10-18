import "./bootstrap";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

$(document).ajaxError(function (event, xhr) {
    if (xhr.status === 401) {
        Swal.fire({
            icon: "warning",
            title: "Sesi berakhir",
            text: "Silakan login kembali.",
        }).then(() => (window.location.href = "/login"));
    }
    if (xhr.status === 403) {
        Swal.fire({
            icon: "error",
            title: "Akses ditolak",
            text: "Kamu tidak memiliki izin.",
        });
    }
});
