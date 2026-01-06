import "./bootstrap";

import Alpine from "alpinejs";

import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.css";

window.Alpine = Alpine;

Alpine.start();

function parseBool(val) {
    if (val === undefined || val === null) return undefined;
    return String(val) === "true" || String(val) === "1";
}

window.initTomSelectAll = () => {
    document.querySelectorAll("select.tom-select").forEach((el) => {
        if (el.tomselect) return; // already initialized

        // optional: enable search toggle per select
        // <select class="tom-select" data-search="false">
        const search = parseBool(el.dataset.search);
        if (search === false) {
            options.controlInput = null; // disable typing search
        }

        new TomSelect(el, {
            create: false,
            allowEmptyOption: false,
            placeholder: el.dataset.placeholder || "-- Pilih --",
        });
    });
};

document.addEventListener("DOMContentLoaded", () => {
    window.initTomSelectAll();
});
