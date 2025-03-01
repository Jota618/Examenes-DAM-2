document.addEventListener('DOMContentLoaded', async () => {
    const urlParams = new URLSearchParams(window.location.search);
    const formContainer = document.getElementById('dynamicForm');
    const staticFieldsContainer = document.getElementById('staticFields');
    const dynamicFieldsContainer = document.getElementById('dynamicFieldsContainer');

    try {
        // Fetch and parse the XML file
        const response = await fetch('modelos/' + urlParams.get('f') + '.xml');
        const xmlText = await response.text();
        const parser = new DOMParser();
        const xml = parser.parseFromString(xmlText, 'application/xml');

        // Render static fields
        const staticFields = xml.querySelectorAll('fields > field');
        staticFields.forEach(field => {
            renderField(field, staticFieldsContainer);
        });

        // Render dynamic fields
        const dynamicFieldGroups = xml.querySelectorAll('dynamicFields > fieldGroup');
        dynamicFieldGroups.forEach(fieldGroup => {
            renderDynamicFieldGroup(fieldGroup, dynamicFieldsContainer);
        });

        // Handle form submission
        formContainer.addEventListener('submit', async (event) => {
            event.preventDefault();

            // Collect static field values
            const staticData = {};
            staticFields.forEach(field => {
                const name = field.querySelector('name').textContent;
                staticData[name] = document.querySelector(`[name="${name}"]`).value;
            });

            // Collect dynamic field group values
            const dynamicData = [];
            dynamicFieldsContainer.querySelectorAll('.dynamic-group').forEach(group => {
                const groupData = Array.from(group.querySelectorAll('.dynamic-line')).map(line => {
                    const lineData = {};
                    line.querySelectorAll('input').forEach(input => {
                        lineData[input.name] = input.value;
                    });
                    return lineData;
                });
                dynamicData.push(groupData);
            });

            // Send collected data
            const formData = {
                staticData,
                dynamicData,
            };

            try {
                const response = await fetch('guardaxml.php?f=' + urlParams.get('f'), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData),
                });

                const result = await response.text();
                console.log(result);
                alert('Form submitted successfully!');
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while submitting the form.');
            }
        });
    } catch (error) {
        console.error('Error loading XML:', error);
    }

    // Function to render a single field
    function renderField(field, container) {
        const name = field.querySelector('name').textContent;
        const type = field.querySelector('type').textContent;
        const placeholder = field.querySelector('placeholder').textContent;
        const required = field.querySelector('required').textContent === 'true';

        const input = document.createElement('input');
        input.type = type;
        input.name = name;
        input.placeholder = placeholder;
        input.required = required;
        container.appendChild(input);
        container.appendChild(document.createElement('br'));
    }

    // Function to render dynamic field groups
    function renderDynamicFieldGroup(fieldGroup, container) {
        const groupName = fieldGroup.getAttribute('name');
        const fields = Array.from(fieldGroup.querySelectorAll('field'));

        const groupContainer = document.createElement('div');
        groupContainer.classList.add('dynamic-group');
        container.appendChild(groupContainer);

        const addLineButton = document.createElement('button');
        addLineButton.type = 'button';
        addLineButton.textContent = `+ Add ${groupName}`;
        addLineButton.classList.add('add-line-btn');
        container.appendChild(addLineButton);

        function renderDynamicLine() {
            const line = document.createElement('div');
            line.classList.add('dynamic-line');

            fields.forEach(field => {
                const name = field.querySelector('name').textContent;
                const type = field.querySelector('type').textContent;
                const placeholder = field.querySelector('placeholder').textContent;
                const required = field.querySelector('required').textContent === 'true';

                const input = document.createElement('input');
                input.type = type;
                input.name = `${groupName}[${name}][]`;
                input.placeholder = placeholder;
                input.required = required;
                line.appendChild(input);
            });

            const removeButton = document.createElement('button');
            removeButton.type = 'button';
            removeButton.textContent = '-';
            removeButton.classList.add('remove-line-btn');
            removeButton.addEventListener('click', () => {
                groupContainer.removeChild(line);
            });
            line.appendChild(removeButton);

            groupContainer.appendChild(line);
        }

        addLineButton.addEventListener('click', renderDynamicLine);
        renderDynamicLine(); // Render initial line
    }
});
