/**
 * Created by plazm on 6/3/14.
 */
$(".basket tbody").on("change","input",function() {
    var input = $(this);
    var row = input.closest("tr");
    var name = input.attr("name");
    name = name.substring(8).substr(0,name.length - 9);
    var value = input.val();


    $.post("",{single: true, key: name, value: value},function(html) {
        row.replaceWith(html);
    });
});