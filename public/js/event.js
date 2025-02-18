

// Image Cropper
document.addEventListener('DOMContentLoaded', function () {

    const imageMaxSize = window.appConfig.imageMaxSize; // Get max image size in bytes
    const imageSizeErrorMsg = window.appConfig.imageSizeErrorMsg;

    let cropper;
    let modal = document.getElementById('imageModal');
    let imageInput = document.getElementById('imageInput');
    let modalImage = document.getElementById('modalImage');
    let croppedImagePreview = document.getElementById('croppedImagePreview');
    let previewContainer = document.getElementById('previewContainer');
    let cropButton = document.getElementById('cropButton');
    let cancelCrop = document.getElementById('cancelCrop');
    let croppedImageData = document.getElementById('croppedImageData'); // Hidden input
    let errorMessage = document.createElement("p"); // Create error message element

    let oldImage = document.getElementById("croppedImageData").value;
    if (oldImage) {
        document.getElementById("previewContainer").classList.remove("hidden");
        document.getElementById("croppedImagePreview").src = oldImage;
    }

    // Function to validate image file size
    function validateFileSize(file) {
        if (file.size > imageMaxSize) {
            //errorMessage.textContent = "Image size must be less than " + imageMaxSize / (1024 * 1024) + " MB.";
            errorMessage.textContent = imageSizeErrorMsg.replace(':size', (imageMaxSize / (1024 * 1024)).toFixed(2));
            errorMessage.classList.add('text-red-500', 'text-sm'); // Add error styling
            imageInput.parentNode.appendChild(errorMessage);
            return false; // File too large
        }
        return true; // File is valid
    }

    // Open Modal When Image Selected
    imageInput.addEventListener('change', function (event) {

        // Clear previous error message
        if (errorMessage.parentNode) {
            errorMessage.parentNode.removeChild(errorMessage);
        }

        let file = event.target.files[0];
        if (file) {

            // Validate file size before opening the cropper modal
            if (!validateFileSize(file)) {
                // Stop execution if the file is too large
                imageInput.value = ""; // Reset the input to clear the selected file
                return; // Prevent the modal from opening
            }

            let reader = new FileReader();
            reader.onload = function (e) {
                modalImage.src = e.target.result;
                modal.classList.remove('hidden');

                if (cropper) {
                    cropper.destroy();
                }

                cropper = new Cropper(modalImage, {
                    aspectRatio: 16 / 9,
                    viewMode: 2,
                    autoCropArea: 1,
                    responsive: true,
                    dragMode: 'move'
                });
            };
            reader.readAsDataURL(file);
        }
    });

    // Crop & Save Image
    cropButton.addEventListener('click', function () {
        if (cropper) {
            let croppedCanvas = cropper.getCroppedCanvas({
                width: 800, // Resize width
                height: 450 // Resize height
            });

            if (croppedCanvas) {
                let croppedImageBase64 = croppedCanvas.toDataURL('image/jpeg');
                croppedImagePreview.src = croppedImageBase64;
                croppedImageData.value = croppedImageBase64; // Store base64 data in hidden input
                previewContainer.classList.remove('hidden');
                modal.classList.add('hidden');
            }
        }
    });

    // Cancel Crop
    cancelCrop.addEventListener('click', function () {
        modal.classList.add('hidden');
    });
});

// jQuery Validation
$(document).ready(function() {

    const eventDateMin = window.appConfig.eventDateMin; // Get min event date minutes
    const eventDateErrorMsg = window.appConfig.eventDateErrorMsg;
    const eventDateRequiredMsg = window.appConfig.eventDateRequiredMsg;
    const titleRequiredMsg = window.appConfig.titleRequiredMsg;
    const titleMaxlengthMsg = window.appConfig.titleMaxlengthMsg;
    const descriptionRequiredMsg = window.appConfig.descriptionRequiredMsg;
    const descriptionMaxlengthMsg = window.appConfig.descriptionMaxlengthMsg;
    const venueRequiredMsg = window.appConfig.venueRequiredMsg;
    const venueMaxlengthMsg = window.appConfig.venueMaxlengthMsg;
    const cropImgRequiredMsg = window.appConfig.cropImgRequiredMsg;

    // Initialize TinyMCE
    tinymce.init({
        selector: '#description',
        setup: function(editor) {
            editor.on('change', function() {
                tinymce.triggerSave(); // Sync TinyMCE data with textarea
                $("#eventForm").validate().element("#description"); // Revalidate the field
            });
        }
    });

    // Custom method to validate event date (must be at least 15 minutes in the future)
    $.validator.addMethod("eventDateFuture", function (value, element) {
        if (!value) return false; // Ensure date is provided
    
        let selectedDate = new Date(value);
        let now = new Date();
        now.setMinutes(now.getMinutes() + eventDateMin); // Use dynamic min time from config
    
        return selectedDate >= now;
    }, eventDateErrorMsg.replace(':minutes', eventDateMin));


    var validator = $("#eventForm").validate({
        ignore: "",
        rules: {
            title: {
                required: true,
                maxlength: 50
            },
            event_date: {
                required: true,
                eventDateFuture: true
            },
            description: {
                required: function() {
                    tinymce.triggerSave(); // Ensure TinyMCE content is updated
                    return $.trim($("#description").val()).length === 0; // Check if empty
                    //return true; // Return validation rule
                },
                maxlength: 500
            },
            venue: {
                required: true,
                maxlength: 100
            },
            cropped_image: {
                required: function() {
                    return $("#croppedImageData").val().trim() === "";
                }
            }
        },
        messages: {
            event_date: {
                required: eventDateRequiredMsg,
                eventDateFuture: eventDateErrorMsg.replace(':minutes', eventDateMin)
            },
            title: {
                required: titleRequiredMsg,
                maxlength: titleMaxlengthMsg.replace(':number', 50)
            },
            description: {
                required: descriptionRequiredMsg,
                maxlength: descriptionMaxlengthMsg.replace(':number', 500)
            },
            venue: {
                required: venueRequiredMsg,
                maxlength: venueMaxlengthMsg.replace(':number', 100)
            },
            cropped_image: {
                required: cropImgRequiredMsg
            }
        },
        errorClass: "text-red-500 text-sm", // Error message styling
        errorPlacement: function(error, element) {
            if (element.attr("name") === "description") {
                // Find the TinyMCE container and place the error after it
                error.insertAfter($(".tox-tinymce"));
            } else {
                error.insertAfter(element);
            }
        }
    });

    // Prevent form submission if validation fails
    $("#eventForm").on("submit", function (e) {
        tinymce.triggerSave(); // Ensure TinyMCE content is updated before validation

        if (!validator.form()) {
            e.preventDefault(); // Prevent submission if form is invalid
        }
    });
});