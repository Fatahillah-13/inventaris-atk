document.addEventListener("DOMContentLoaded", function () {
    // Pastikan tom-select sudah ter-init oleh app.js
    if (window.initTomSelectAll) window.initTomSelectAll();

    const divisionEl = document.getElementById("division_id");
    const itemEl = document.getElementById("item_id");

    if (!divisionEl || !itemEl) return;

    const divisionTom = divisionEl.tomselect;
    const itemTom = itemEl.tomselect;

    if (!divisionTom || !itemTom) {
        console.error(
            "Tom Select belum ter-init untuk #division_id / #item_id. Pastikan resources/js/app.js ke-load."
        );
        return;
    }

    function resetItems(message) {
        itemTom.clear(true);
        itemTom.clearOptions();
        itemTom.addOption({ value: "", text: message, disabled: true });
        itemTom.addItem("", true);
        itemTom.disable();
    }

    async function loadItemsByDivision(divisionId) {
        resetItems("Memuat barang...");

        try {
            const res = await fetch(
                `/ajax/items-by-division/${encodeURIComponent(divisionId)}`
            );
            const data = await res.json();

            itemTom.enable();
            itemTom.clear(true);
            itemTom.clearOptions();
            itemTom.addOption({ value: "", text: "-- Pilih Barang --" });

            if (!Array.isArray(data) || data.length === 0) {
                resetItems("Tidak ada barang di divisi ini");
                return;
            }

            let selectableCount = 0;
            let lastSelectableId = null;

            data.forEach((item) => {
                const stok = Number(item.stok || 0);

                itemTom.addOption({
                    value: String(item.id),
                    text: `${item.nama_barang} - stok: ${stok}`,
                    disabled: stok <= 0,
                });

                if (stok > 0) {
                    selectableCount++;
                    lastSelectableId = String(item.id);
                }
            });

            itemTom.refreshOptions(false);

            if (selectableCount === 1 && lastSelectableId) {
                itemTom.setValue(lastSelectableId, true);
            }
        } catch (e) {
            console.error(e);
            resetItems("Gagal memuat barang");
        }
    }

    divisionTom.on("change", function (value) {
        if (!value) {
            resetItems("-- Pilih divisi terlebih dahulu --");
            return;
        }
        loadItemsByDivision(value);
    });

    // initial state / old input
    const initialDivision = divisionEl.value;
    if (initialDivision) {
        loadItemsByDivision(initialDivision);
    } else {
        resetItems("-- Pilih divisi terlebih dahulu --");
    }
});
