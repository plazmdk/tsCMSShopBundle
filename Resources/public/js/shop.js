$(document).on("click",".modalToggle", function(e) {
    e.preventDefault();
    var toggle = $(this);
    toggle.nextAll(".modal").modal();
});
$(document).on("change", ".priceNoVat", function() {
    var inputNoVat = $(this);
    var val = inputNoVat.val().replace(",",".");

    var form = inputNoVat.closest("form,.box");
    var priceRow = inputNoVat.closest(".row");
    var inputVat = priceRow.find(".priceVat");
    if (inputVat) {
        var vatGroup = form.find(".vatGroup");
        var percentage = parseFloat(vatGroup.find("option:selected").data("percentage"));
        inputVat.val(formatPrice(val * (100 + percentage) / 100));
    }

    inputNoVat.val(formatPrice(val));
});
$(document).on("change", ".priceVat", function() {
    var inputVat = $(this);
    var val = inputVat.val().replace(",",".");

    var form = inputVat.closest("form,.box");
    var priceRow = inputVat.closest(".row");
    var inputNoVat = priceRow.find(".priceNoVat");

    var percentage = parseFloat(vatGroup.find("option:selected").data("percentage"));

    inputNoVat.val(formatPrice(val * 100 / (100 + percentage)));
    inputVat.val(formatPrice(val));
});
$(document).on("change", ".vatGroup", function() {
    var vatGroup = $(this);
    var form = vatGroup.closest("form,.box");

    form.find(".priceNoVat").change();
});
$(".priceNoVat").change();

function formatPrice(price) {
    return parseFloat(Math.round(price * 100) / 100).toFixed(2).replace(".",",");
}

(function() {
    var original = false;

    $(".productTitle")
        .on("focus",function() {
            var form = $(this).closest("form");
            var productTitle = form.find(".productTitle").val();

            var pagePath = form.find(".productPath");
            original = pagePath.val() == ("/"+convertTitleToPath(productTitle)).replace(/\/\//g,'/') || pagePath.val() == "";
        })
        .on("change",function() {
            if (!original) {
                return;
            }
            var form = $(this).closest("form");
            var productTitle = form.find(".productTitle").val();

            var productPath = form.find(".productPath");
            productPath.val(("/"+convertTitleToPath(productTitle)).replace(/\/\//g,'/')).change();
        });

    function convertTitleToPath(title) {
        return title
            .toLowerCase()
            .replace(/ /g,'-')
            .replace(/æ/g,'ae')
            .replace(/ø/g,'oe')
            .replace(/å/g,'aa')
            .replace(/[^\x00-\x7F]/g, "");
    }
})();