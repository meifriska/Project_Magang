document.addEventListener("DOMContentLoaded", function () {

    const jenisSelect = document.getElementById("jenis");
    const instansiSelect = document.getElementById("instansi");

    function loadInstansi(jenis, selected = "") {
        if (!jenis) return;

        fetch("../auth/get_instansi.php?jenis=" + jenis + "&selected=" + selected)
            .then(res => res.text())
            .then(data => {
                instansiSelect.innerHTML = data;
            });
    }

    // 🔥 INI KUNCI (AMBIL DARI PHP)
    let jenisAwal = document.getElementById("jenis").value;
    let selectedInstansi = "<?= $id_instansi ?>";

    if (jenisAwal) {
        loadInstansi(jenisAwal, selectedInstansi);
    }

    jenisSelect.addEventListener("change", function () {
        loadInstansi(this.value);
    });

});