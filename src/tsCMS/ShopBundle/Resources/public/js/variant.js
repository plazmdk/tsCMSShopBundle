$(".variant input[type='checkbox']").change(function() {
    var variant = $(this);
    var checked = variant.is(":checked");

    var nextSibling = variant.closest(".variant").next();
    while (nextSibling.length && !nextSibling.is(".variant")) {
        nextSibling.find("input[type='checkbox']").prop("checked", checked);
        nextSibling = nextSibling.next();
    }
});
$("#createVariantsForm").submit(function() {
    var variantCount = 1;
    $(".variant input:checked").each(function() {
        var variantoptions = 0;
        var nextSibling = variant.closest(".variant").next();
        while (nextSibling.length && !nextSibling.is(".variant")) {
            if (nextSibling.find("input[type='checkbox']").is(":checked")) {
                variantoptions++;
            }
            nextSibling = nextSibling.next();
        }
        variantCount = variantCount * variantoptions;
    });
    return confirm("Dette vil oprette "+variantCount+" varianter\nEr du sikker?");
});