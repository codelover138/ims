<div class="gamma-generated-form"><form method="post" action="http://localhost/ims/admin/gamma/submit_form/5"><div class="gamma-generated-header"><h3>POPOP</h3></div><div class="row"><div class="col-md-12"><div class="form-group gamma-generated-field"><label>First Name</label><input type="text" name="first_name" value="" class="form-control"></div></div></div><div class="row"><div class="col-md-12"><div class="form-group gamma-generated-field"><label>Last Name</label><input type="text" name="last_name" value="" class="form-control"></div></div></div><div class="row"><div class="col-md-12"><div class="form-group gamma-generated-field"><label>Sex</label><select name="sex" class="form-control"><option value="Male">Male</option><option value="Female">Female</option></select></div></div></div><div class="row"><div class="col-md-12"><div class="form-group gamma-generated-field"><label>Availablity</label><input type="text" name="availablity" value="" class="form-control"></div></div></div><div class="form-group"><button type="submit" class="btn btn-primary">Submit</button></div></form></div><script>
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