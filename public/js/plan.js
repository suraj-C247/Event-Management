// jQuery Validation
$(document).ready(function() {

    var validator = $("#planForm").validate({
        ignore: "",
        rules: {
            name: {
                required: true,
                maxlength: 50
            },
            price: {
                required: true,
                min: 1
            },
            type: { 
                required: true,
            },
            description: {
                required: true,
                maxlength: 250
            },
        },
        errorClass: "text-red-500 text-sm", // Error message styling
        errorPlacement: function(error, element) {
            error.insertAfter(element);
        }
    });

    // Prevent form submission if validation fails
    $("#planForm").on("submit", function (e) {

        if (!validator.form()) {
            e.preventDefault(); // Prevent submission if form is invalid
        }
    });
});