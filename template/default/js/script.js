$(function() {
    changeRangedInputFields();
});

function changeRangedInputFields() {
    var ranged_input  = $(".range-input");
    var ranged_output = $(".range-output");
    
    ranged_output.html(ranged_input.attr('value'));
    
    ranged_input.on('input', function() {
        ranged_output.html(this.value)
    });
}