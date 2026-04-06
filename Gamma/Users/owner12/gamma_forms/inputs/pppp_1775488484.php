<div class="gamma-generated-form"><form method="post" action="http://localhost/ims/admin/gamma/submit_form/6"><div class="gamma-generated-header"><h3>pppp</h3></div><div class="row"><div class="col-md-12"><div class="form-group gamma-generated-field"><label>Name</label><input type="text" name="name" value="" class="form-control"></div></div></div><div class="row"><div class="col-md-12"><div class="form-group gamma-generated-field"><label>Description</label><input type="text" name="description" value="" class="form-control"></div></div></div><div class="form-group"><button type="submit" class="btn btn-primary">Submit</button></div></form></div><script>
document.addEventListener("click", function (event) {
    if (event.target.matches(".gamma-add-row")) {
        var table = document.querySelector("table[data-table-number='" + event.target.getAttribute("data-table-number") + "']");
        if (!table) { return; }
        var body = table.querySelector("tbody");
        var firstRow = body.querySelector("tr");
        if (!firstRow) { return; }
        var clone = firstRow.cloneNode(true);
        var nextIndex = body.querySelectorAll("tr").length;
        Array.prototype.forEach.call(clone.querySelectorAll("input, select, textarea"), function (field) {
            if (!field.name) { return; }
            field.name = field.name.replace(/gamma_tables\[(\d+)\]\[\d+\]/, "gamma_tables[$1][" + nextIndex + "]");
            if (field.type === "checkbox" || field.type === "radio") {
                field.checked = false;
            } else {
                field.value = "";
            }
        });
        body.appendChild(clone);
    }
    if (event.target.matches(".gamma-remove-row")) {
        var table = document.querySelector("table[data-table-number='" + event.target.getAttribute("data-table-number") + "']");
        if (!table) { return; }
        var body = table.querySelector("tbody");
        if (body.children.length > 1) {
            body.removeChild(body.lastElementChild);
        }
    }
});
</script>