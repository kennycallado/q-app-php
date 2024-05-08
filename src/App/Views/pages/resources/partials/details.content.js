const resource_details = document.getElementById('resource-details');
const hidden = resource_details.querySelector('#hidden-items');

const list = resource_details.querySelector('#list');
const item_add = resource_details.querySelector('#item-add');
const item_input = resource_details.querySelector('#item-input');

// add listeners
item_add.addEventListener('click', processInput);
item_input.addEventListener('keypress', handler);

function handler(e) {
    if (e.key !== 'Enter') return;
    processInput();
}

function processInput() {
    const item = item_input.value.trim();
    if (!item) return;

    list.appendChild(createItem(item));
    hidden.appendChild(createHidden(item));

    item_input.value = '';
}

function createItem(item) {
    const li = document.createElement('li');
    li.className = 'list-group-item d-flex justify-content-between align-items-center';
    li.innerHTML = `
        <span>${item}</span>
        <span style="cursor: pointer;" onclick="removeItem(this)">❌</span>
    `;

    return li;
}

function createHidden(item) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = '{{ resource.type }}[]';
    input.value = item;

    return input;
}
