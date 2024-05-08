const project_details = document.getElementById('project-details');
// const form = project_details.querySelector('form');
const list = project_details.querySelector('#list');
const key_add = project_details.querySelector('#key-add');
const key_input = project_details.querySelector('#key-input');
const hidden_keys = project_details.querySelector('#hidden-keys');

// Event handling functions
function handleKeyPressEvent(e) {
    if (e.key !== 'Enter') return;
    processKeyInput();
}

function handleAddButtonClick() {
    processKeyInput();
}

function processKeyInput() {
    const key = key_input.value.trim();
    if (key === '') return; // Exit early if key is empty
    if (isKeyAlreadyExists(key)) {
        alert('Key already exists.');
        return; // Exit early if key already exists
    }
    addKeyToProject(key);
    key_input.value = '';
}

// Attach event listeners
key_input.addEventListener('keypress', handleKeyPressEvent);
key_add.addEventListener('click', handleAddButtonClick);

// Function to add a key to the project
function addKeyToProject(key) {
    const input = createInput(key);
    const item = createItem(key);
    appendKeyAndItem(input, item);
}

// Function to check if a key already exists in the hidden_keys section
function isKeyAlreadyExists(key) {
    return !!hidden_keys.querySelector(`input[value="${key}"]`);
}

// Function to create a hidden input element for the key
function createInput(key) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'keys[]';
    input.value = key;
    return input;
}

// Function to create a list item for the key
function createItem(key) {
    const li = document.createElement('li');
    li.className = 'list-group-item d-flex justify-content-between align-items-center';
    li.innerHTML = `
        <span>${key}</span>
        <span style="cursor: pointer;" onclick="removeItemAndInput(this)">❌</span>
    `;
    return li;
}

// Function to append a key's input and item to the DOM
function appendKeyAndItem(input, item) {
    hidden_keys.appendChild(input);
    list.appendChild(item);
}

// Function to remove a key's input and corresponding item from the DOM
function removeItemAndInput(el) {
    const item = el.parentElement;
    const key = item.querySelector('span').textContent;
    const input = hidden_keys.querySelector(`input[value="${key}"]`);
    hidden_keys.removeChild(input);
    list.removeChild(item);
}
