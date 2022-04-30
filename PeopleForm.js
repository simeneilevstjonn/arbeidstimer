class PeopleForm {
    constructor(element, permissibleSelections) {
        // Set variables
        this.element = element;
        this.options = permissibleSelections;

        // Clear element
        this.element.innerHTML = "";

        // Initialise ID counter
        this.nextId = 0;
    }

    // Appends a new row to the element
    appendRow() {
        // Create the row element
        const row = document.createElement("tr");

        // Create select cell
        const seltd = document.createElement("td");
        row.append(seltd);

        // Create select
        const sel = document.createElement("select");
        sel.className = "selectpicker form-control personselector";
        sel.setAttribute("data-live-search", "true");
        sel.setAttribute("at-personfieldid", this.nextId);
        sel.onchange = this.selectionUpdateHandler.bind(this)
        seltd.append(sel);

        // Append all elements
        // Default element
        const opt = document.createElement("option");
        opt.value = "null";
        opt.className = "d-none";
        opt.disabled = true;
        opt.selected = true;
        opt.textContent = "-";
        sel.append(opt)

        // From options array
        for (const [id, name] of Object.entries(this.options)) {
            const opt = document.createElement("option");
            opt.value = id;
            opt.textContent = name;
            sel.append(opt);
        }

        // Create hour input
        // Create input cell
        const inpttd = document.createElement("td");
        row.append(inpttd);

        // Create input
        const input = document.createElement("input");
        input.className = "form-control hourfield";
        input.type = "number";
        input.min = 0.5;
        input.step = 0.5;
        input.max = 24;
        input.setAttribute("at-personfieldid", this.nextId);

        inpttd.append(input);

        // Create delete key
        const deltd = document.createElement("td");
        row.append(deltd);

        // Create button
        const btn = document.createElement("button");
        deltd.append(btn);
        btn.className = "btn deletekey";
        btn.innerHTML = "&times;"
        btn.style.display = "none";
        btn.setAttribute("at-personfieldid", this.nextId++);
        btn.onclick = this.deleteRow.bind(this);

        // Append row
        this.element.append(row);

        // Init picker
        $(sel).selectpicker('refresh');
    }

    // Row deletion method
    deleteRow(event) {
        // Decrement id if this was the greatest
        if (Number(event.target.getAttribute("at-personfieldid")) + 1 == this.nextId) this.nextId--;

        // Get row
        const row = event.target.parentNode.parentNode;
        row.parentNode.removeChild(row);
    }

    // Selection updater
    selectionUpdateHandler(event) {
        // If this is the last row
        if (Number(event.target.getAttribute("at-personfieldid")) + 1 == this.nextId) {
            // Enable delete key
            event.target.parentNode.parentNode.parentNode.children[2].children[0].style.display = "block";

            // Append a new row
            this.appendRow();
        }

        // Get old id
        const oldid = event.target.getAttribute("at-lastvalue");

        // Disable this value for other pickers, and re-enable the previous one
        // Iterate rows
        for (const row of this.element.children) {
            // Get picker
            const picker = row.children[0].children[0].children[0];

            // Skip if picker is target
            if (picker === event.target) continue;

            // Iterate options
            for (const option of picker.children) {
                // Disable if it matches this id
                if (option.value == event.target.value) {
                    option.disabled = true;
                    option.style.display = "none";
                }
                // Enable if matches old id
                if (oldid != null && option.value == oldid) {
                    option.disabled = false;
                    option.style.display = "";
                }
            }

            // Reload picker
            $(picker).selectpicker('refresh');
        }

        // Set the previous value attribute
        event.target.setAttribute("at-lastvalue", event.target.value);
    }

    // Save data to JSON
    serialise() {
        // Create a dictionary
        let dict = {};

        // Iterate all rows
        for (const row of this.element.children) {
            // Get picker
            const picker = row.children[0].children[0].children[0];

            // Skip if picker has value null
            if (picker.value == "null") continue;

            // Get input
            const input = row.children[1].children[0];

            // Append data
            dict[picker.value] = Number(input.value);
        }

        // Return serialised data
        return JSON.stringify(dict);
    }

    // Validate that all rows with names have a hour count
    validate() {
        let isValid = true;

        // Iterate all rows
        for (const row of this.element.children) {
            // Get picker
            const picker = row.children[0].children[0].children[0];

            // If picker has value null
            if (picker.value == "null" && this.element.childElementCount != 1) continue;
            console.log("here")
            // Get input
            const input = row.children[1].children[0];
            const value = Number(input.value)

            // Check that input is between 0.5 and 24 inclusive, and is a multiple of 0.5.
            if (value < 0.5 || value > 24 || value % 0.5 != 0 || picker.value == "null") {
                // Set input to invalid
                input.className = "form-control hourfield is-invalid";

                // Set valid var to false
                isValid = false;

                // Omit any error message for now
            }
            else {
                // Set input to neutral
                input.className = "form-control hourfield";
            }
        }

        return isValid;
    }
}