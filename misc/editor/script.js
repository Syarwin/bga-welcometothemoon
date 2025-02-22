let $ = (id) => document.getElementById(id);
let DATAS = null;

let modes = localStorage.getItem('wttmModes');
modes = modes === null ? {} : JSON.parse(modes);

const EXCLUSIVE_MODES = ['add', 'move'];
const ONCLICK_MODES = ['add', 'rotate'];

let editor = CodeMirror.fromTextArea($('data'), {
  lineNumbers: true,
  mode: {
    name: 'javascript',
    json: true,
    statementIndent: 2,
  },
  theme: 'abbott',
});

/////////////////////////////////////////////////////////////////////
//  _   _                 ____                            _
// | \ | | _____      __ / ___|  ___ ___ _ __   __ _ _ __(_) ___
// |  \| |/ _ \ \ /\ / / \___ \ / __/ _ \ '_ \ / _` | '__| |/ _ \
// | |\  |  __/\ V  V /   ___) | (_|  __/ | | | (_| | |  | | (_) |
// |_| \_|\___| \_/\_/   |____/ \___\___|_| |_|\__,_|_|  |_|\___/
/////////////////////////////////////////////////////////////////////

$('btn-create').addEventListener('click', async () => {
  if ($('scenario-id').value == '') {
    alert('Please fill a scenario id');
    return;
  }
  if ($('scenario-name').value == '') {
    alert('Please fill a scenario name');
    return;
  }

  // Checking JPG file
  let jpgFile = null;
  let url = $('scenario-jpg').value;
  if (url == null) {
    alert('You must give an URL for the jpg file');
    return;
  }

  let validURL = await testURL(url);
  if (validURL) {
    jpgFile = url;
  } else {
    alert('Invalid jpg file');
    return;
  }

  // Create datas
  let datas = {
    id: $('scenario-id').value,
    name: $('scenario-name').value,
    jpgUrl: jpgFile,
  };

  loadScenario(datas);
  saveScenario();
  $('splashscreen').classList.add('hidden');
});

function testURL(url) {
  return new Promise((resolve, reject) => {
    let tester = new Image();
    tester.addEventListener('load', () => resolve(true));
    tester.addEventListener('error', () => resolve(false));
    tester.src = url;
  });
}

////////////////////////////////////////////////////////////////////////
//  _                    _   ____                            _
// | |    ___   __ _  __| | / ___|  ___ ___ _ __   __ _ _ __(_) ___
// | |   / _ \ / _` |/ _` | \___ \ / __/ _ \ '_ \ / _` | '__| |/ _ \
// | |__| (_) | (_| | (_| |  ___) | (_|  __/ | | | (_| | |  | | (_) |
// |_____\___/ \__,_|\__,_| |____/ \___\___|_| |_|\__,_|_|  |_|\___/
////////////////////////////////////////////////////////////////////////
function getStoredScenarios() {
  let scenarios = localStorage.getItem('wttmScenarios');
  if (scenarios != '' && scenarios != null) {
    scenarios = JSON.parse(scenarios);
  } else {
    scenarios = {};
  }
  return scenarios;
}

let scenarios = getStoredScenarios();
Object.keys(scenarios).forEach((scenarioId) => {
  let option = document.createElement('option');
  option.setAttribute('value', scenarioId);
  option.appendChild(document.createTextNode(scenarios[scenarioId].name));
  $('select-scenario').appendChild(option);
});

// Autoload
let params = new URLSearchParams(document.location.search);
let urlScenarioId = params.get('scenarioId');
if (urlScenarioId !== null && scenarios[urlScenarioId]) {
  loadScenarioFromStorage(urlScenarioId);
  $('splashscreen').classList.add('no-anim');
}

// Load when an option is selected
$('select-scenario').addEventListener('change', () => {
  let scenarioId = $('select-scenario').value;
  if (scenarioId != '') {
    loadScenarioFromStorage(scenarioId);
  }
});

function loadScenarioFromStorage(scenarioId) {
  let datas = scenarios[scenarioId];
  loadScenario(datas);
  $('splashscreen').classList.add('hidden');
}

//loadScenarioFromStorage('Test');

$('form-load-storage').addEventListener('submit', async (evt) => {
  evt.preventDefault();

  let scenarioId = $('select-scenario').value;
  if (scenarioId != '') {
    loadScenarioFromStorage(scenarioId);
    return;
  }

  // Checking Heat file
  let wttmFile = null;
  let fileList = $('load-file').files;
  for (let i = 0; i < fileList.length; i++) {
    let name = fileList[i].name;
    let ext = name.split('.').pop();
    if (ext == 'json') wttmFile = fileList[i];
  }
  if (wttmFile == null) {
    alert('Please either select a file on browser storage or select a wttm file on your computer');
    return;
  }

  let datas = await parseJsonFile(wttmFile);
  loadScenario(datas);
  $('splashscreen').classList.add('hidden');
});

const loadScenarioInput = $('load-file');
loadScenarioInput.addEventListener('change', updateLoadFile, false);

function updateLoadFile() {
  const fileList = loadScenarioInput.files;
  let names = [];
  for (let i = 0; i < fileList.length; i++) {
    names.push(fileList[i].name);
  }

  $('load-file-label').innerHTML = names.length ? names.join(', ') : 'Click here to load .json file';
}
updateLoadFile();

//////////////////////////////////
//  _____    _ _ _
// | ____|__| (_) |_ ___  _ __
// |  _| / _` | | __/ _ \| '__|
// | |__| (_| | | || (_) | |
// |_____\__,_|_|\__\___/|_|
//////////////////////////////////

function loadScenario(datas) {
  DATAS = datas;
  if (!DATAS.sections) DATAS.sections = [];

  $('display-scenario-id').innerHTML = 'ID: ' + DATAS.id;
  $('display-scenario-name').innerHTML = 'Name: ' + DATAS.name;

  // Change JPG
  let jpgFile = DATAS.jpgUrl;
  $('board').style.backgroundImage = `url(${jpgFile})`;

  // Load sections
  DATAS.sections.forEach((section) => this.setupSection(section));

  // Update URL
  let params = new URLSearchParams(document.location.search);
  if (params.get('scenarioId') != DATAS.id) {
    params.set('scenarioId', DATAS.id);
    document.location.search = params;
  }

  // Load css
  var link = document.createElement('link');
  link.rel = 'stylesheet';
  link.type = 'text/css';
  link.href = `./${DATAS.id}.css`;
  document.head.appendChild(link);

  saveScenario();
}

// //////////////////
// //////////////////
// ///  SAVE DATAS
// //////////////////
// //////////////////

function saveScenario() {
  // Load existing scenarios
  let scenarios = getStoredScenarios();
  // Add current scenario infos
  scenarios[DATAS.id] = DATAS;
  localStorage.setItem('wttmScenarios', JSON.stringify(scenarios));
  editor.getDoc().setValue(JSON.stringify(DATAS, null, 2));
  $('save-changes').classList.add('disabled');
}

///////////////////////////////////////////////
///////////////////////////////////////////////
///////////////////////////////////////////////
//  ____            _   _
// / ___|  ___  ___| |_(_) ___  _ __  ___
// \___ \ / _ \/ __| __| |/ _ \| '_ \/ __|
//  ___) |  __/ (__| |_| | (_) | | | \__ \
// |____/ \___|\___|\__|_|\___/|_| |_|___/
///////////////////////////////////////////////
///////////////////////////////////////////////
///////////////////////////////////////////////

// Toggle a mode (update button, storage, and update class)
function toggleMode(section, mode, val = null) {
  modes[section][mode] = val === null ? !modes[section][mode] : val;
  $('main-frame').classList.toggle(`${mode}-${section}`, modes[section][mode]);
  $(`${mode}-${section}`).classList.toggle('active', modes[section][mode]);

  // Exclusive modes
  if (EXCLUSIVE_MODES.includes(mode) && modes[section][mode]) {
    DATAS.sections.forEach((config) => {
      let section2 = config.id;

      config.modes.forEach((mode2) => {
        if (section2 == section && mode2 == mode) return;
        if (!EXCLUSIVE_MODES.includes(mode2)) return;
        if (!modes[section2]) return;

        modes[section2][mode2] = false;
        $('main-frame').classList.remove(`${mode2}-${section2}`);
        if ($(`${mode2}-${section2}`)) $(`${mode2}-${section2}`).classList.remove('active');
      });
    });
  }

  // Save into local storage
  localStorage.setItem('wttmModes', JSON.stringify(modes));
}

// Add a section in toolbar and setup events
function setupSection(config) {
  let section = config.id;
  modes[section] = modes[section] || {
    show: false,
  };

  $('add-section').insertAdjacentHTML(
    'beforebegin',
    `<div class="section" id="section-${section}">
  <h2>${config.name}</h2>
  <div class="toolbar"></div>
</div>`
  );

  let icons = {
    show: 'eye',
    add: 'plus',
    rotate: 'compute',
    move: 'move',
  };

  if (!config.modes) config.modes = [];

  config.modes.forEach((mode) => {
    // HTML
    $(`section-${section}`)
      .querySelector('.toolbar')
      .insertAdjacentHTML('beforeend', `<button id="${mode}-${section}"><i class="fa-icon fa-${icons[mode]}"></i></button>`);

    // Event listener
    $(`${mode}-${section}`).addEventListener('click', () => toggleMode(section, mode));
    toggleMode(section, mode, modes[section][mode] || false);

    // ADD MODE
    if (mode == 'add' && config.elts) {
      config.elts.forEach((elt) => addSlot(elt.id, config.eltClass, elt.x, elt.y, elt.r || 0));
    }
  });
}

function addSlot(id, className, x, y, r = 0) {
  $('board').insertAdjacentHTML('beforeend', `<div class='wttm-slot ${className}' id='slot-${id}' data-id='${id}'></div>`);
  $(`slot-${id}`).style.left = x + 'px';
  $(`slot-${id}`).style.top = y + 'px';
  if (r != 0) {
    $(`slot-${id}`).style.transform = `rotate(${r}deg)`;
  }
}

function moveSlot(slot, dx, dy) {
  let oSlot = $(`slot-${dragged.id}`);
  slot.x += dx;
  slot.y += dy;
  oSlot.style.left = `${slot.x}px`;
  oSlot.style.top = `${slot.y}px`;
  saveScenario();
}

// Click on Board
function onClickBoard(sectionConfig, mode, pos, evt) {
  // ADD MODE
  if (mode == 'add') {
    let maxId = 0;
    $('board')
      .querySelectorAll('.wttm-slot')
      .forEach((e) => (maxId = Math.max(maxId, parseInt(e.dataset.id))));
    let id = maxId + 1;

    addSlot(id, sectionConfig.eltClass, pos.x, pos.y);
    sectionConfig.elts.push({ id, x: pos.x, y: pos.y });
    saveScenario();
  }

  // ROTATE MODE
  if (mode == 'rotate') {
    const DELTA = 90;
    let slotId = evt.target.classList.contains('wttm-slot') ? evt.target.dataset.id : null;
    if (slotId == null) return;

    sectionConfig.elts.forEach((elt) => {
      if (elt.id != parseInt(slotId)) return;

      elt.r = (elt.r || 0) + (evt.ctrlKey ? -DELTA : DELTA);
      evt.target.style.transform = `rotate(${elt.r}deg)`;
    });
    saveScenario();
  }
}

///////////////////////////////////////////////////////////////////////////
//  _____                 _     _   _                 _ _
// | ____|_   _____ _ __ | |_  | | | | __ _ _ __   __| | | ___ _ __ ___
// |  _| \ \ / / _ \ '_ \| __| | |_| |/ _` | '_ \ / _` | |/ _ \ '__/ __|
// | |___ \ V /  __/ | | | |_  |  _  | (_| | | | | (_| | |  __/ |  \__ \
// |_____| \_/ \___|_| |_|\__| |_| |_|\__,_|_| |_|\__,_|_|\___|_|  |___/
///////////////////////////////////////////////////////////////////////////

// Click on board
$('main-frame').addEventListener('click', (evt) => {
  let wrapper = $('main-frame-wrapper');
  let x = evt.x - wrapper.offsetLeft + wrapper.scrollLeft;
  let y = evt.y - wrapper.offsetTop + wrapper.scrollTop;

  // Do we have any listening mode ?
  DATAS.sections.forEach((config) => {
    let section = config.id;

    config.modes.forEach((mode) => {
      if (!ONCLICK_MODES.includes(mode)) return;
      if (!modes[section][mode]) return;

      // Callback
      onClickBoard(config, mode, { x, y }, evt);
    });
  });
});

// Dragndrop elements
let dragged = null;
$('main-frame').addEventListener('mousedown', (evt) => {
  // Are we listening to MOVE mode
  DATAS.sections.forEach((config) => {
    let section = config.id;
    if (!config.modes.includes('move') || !modes[section].move) return;
    if (!evt.target.classList.contains(config.eltClass)) return;

    config.elts.forEach((elt) => {
      if (elt.id == evt.target.dataset.id) {
        dragged = elt;
      }
    });
  });
});

$('main-frame').addEventListener('mouseup', (evt) => {
  if (dragged) {
    moveSlot(dragged, evt.movementX, evt.movementY);
  }
  dragged = null;
});

// Move over slots
$('main-frame').addEventListener('mousemove', (evt) => {
  let wrapper = $('main-frame-wrapper');
  let x = evt.x - wrapper.offsetLeft + wrapper.scrollLeft;
  let y = evt.y - wrapper.offsetTop + wrapper.scrollTop;
  $('hover-indicator').style.left = x + 'px';
  $('hover-indicator').style.top = y + 'px';

  $('cell-indicator-counter').innerHTML = evt.target.classList.contains('wttm-slot') ? evt.target.id : '----';

  // Dragndrop
  if (dragged) {
    moveSlot(dragged, evt.movementX, evt.movementY);
  }
});

// Add new section
$('add-section').addEventListener('click', () => {
  let id = prompt('ID of the section ?');
  if (id == '') return;

  DATAS.sections.push({
    id: id,
    name: capitalizeFirstLetter(id),
    modes: ['show', 'add', 'move'],
    eltClass: `slot-${id.slice(0, -1)}`,
    elts: [],
  });
  saveScenario();
  window.location.reload();
});

// Re-index
$('re-index').addEventListener('click', () => {
  if (!confirm('Are you sure?')) return;

  let id = 1;
  DATAS.sections.forEach((section) => {
    section.elts.forEach((elt) => (elt.id = id++));
  });
  saveScenario();
  window.location.reload();
});

// Manual edit of JSON
editor.on('change', () => $('save-changes').classList.remove('disabled'));
$('save-changes').addEventListener('click', () => {
  if ($('save-changes').classList.contains('disabled')) return;

  try {
    DATAS = JSON.parse(editor.getValue());
    saveScenario();
    window.location.reload();
  } catch (e) {
    alert('Parsing error:' + e);
  }
});

// Check duplicates
$('check-duplicates').addEventListener('click', () => {
  DATAS.sections.forEach((section) => {
    section.elts.forEach((elt) => {
      DATAS.sections.forEach((section2) => {
        section2.elts.forEach((elt2) => {
          if (elt.id < elt2.id && Math.abs(elt.x - elt2.x) + Math.abs(elt.y - elt2.y) < 3) {
            console.log('Probable duplicate: ' + elt.id + ' / ' + elt2.id);
          }
        });
      });
    });
  });
});

////////////////////////
//  _   _ _   _ _
// | | | | |_(_) |___
// | | | | __| | / __|
// | |_| | |_| | \__ \
//  \___/ \__|_|_|___/
////////////////////////

async function parseJsonFile(file) {
  return new Promise((resolve, reject) => {
    const fileReader = new FileReader();
    fileReader.onload = (event) => resolve(JSON.parse(event.target.result));
    fileReader.onerror = (error) => reject(error);
    fileReader.readAsText(file);
  });
}

function capitalizeFirstLetter(string) {
  return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
}
