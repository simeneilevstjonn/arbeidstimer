// Form submitting
function submitForm() {
    let isValid = true;
    // Validate main form
    if (!validateForm()) isValid = false;
    // Validate people
    if (!form.validate()) isValid = false;

    // Exit if invalid
    if (!isValid) return false;

    // Serialise people
    document.querySelector("#peopleInput").value = form.serialise();

    // Submit
    document.querySelector("#mainForm").submit();
}

// Validate the main form
function validateForm() {
    let isValid = true;

    // Get form
    const mainForm = document.querySelector("#mainForm");

    // Date
    const date = document.querySelector("#dateInput");

    if (date.value == "") {
        // Set to invalid
        date.className = "form-control is-invalid";
        isValid = false;
    }
    else {
        // Set to neutral
        date.className = "form-control";
    }

    // Supervisor
    const supervisor = document.querySelector("#supervisorInput");

    if (supervisor.value == "null") {
        // Set to invalid
        supervisor.parentNode.className = "dropdown bootstrap-select form-control is-invalid";
        isValid = false;
    }
    else {
        // Set to neutral
        supervisor.parentNode.className = "dropdown bootstrap-select form-control";
    }

    // Date
    const description = document.querySelector("#descriptionInput");

    if (description.value == "") {
        // Set to invalid
        description.className = "form-control is-invalid";
        isValid = false;
    }
    else {
        // Set to neutral
        description.className = "form-control";
    }

    return isValid;
}