$(document).on("change",".priceCalc",function() {
    var input = $(this), target = null, factor = 1;
    var wrapper = input.closest(".form-group").parent().parent();
    var percentage = parseFloat(wrapper.find("select[data-price-group='"+input.data("price-group")+"']").find("option:selected").data("percentage"));
    if (input.data("price-vat")) {
        target = wrapper.find(".priceCalc[data-price-group='"+input.data("price-group")+"'][data-price-vat='false']");
        factor = 100 / (100 + percentage);
    } else {
        target = wrapper.find(".priceCalc[data-price-group='"+input.data("price-group")+"'][data-price-vat='true']");
        factor = (100 + percentage) / 100;
    }
    var value = input.val().replace(",",".");
    target.val(formatPrice(value * factor));
    input.val(formatPrice(value));
});
$(document).on("change","select[data-price-group]", function() {
    var wrapper = $(this).closest(".form-group").parent().parent();
    wrapper.find(".priceCalc[data-price-group='"+$(this).data("price-group")+"'][data-price-vat='false']").change();
});
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