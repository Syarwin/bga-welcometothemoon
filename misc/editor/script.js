let $ = (id) => document.getElementById(id);
let DATAS = null;

let modes = localStorage.getItem('wttmModes');
modes = modes === null ? {} : JSON.parse(modes);

const EXCLUSIVE_MODES = ['add'];
const ONCLICK_MODES = ['add'];

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
    if (ext == 'wttm') wttmFile = fileList[i];
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

  $('load-file-label').innerHTML = names.length ? names.join(', ') : 'Click here to load .wttm file';
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
  // DATAS.sections = [
  //   {
  //     id: 'numbers',
  //     name: 'Numbers',
  //     modes: ['show', 'add'],
  //     elts: [],
  //     eltClass: 'slot-number',
  //   },
  // ];

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

  saveScenario();
}

// //////////////////
// //////////////////
// ///  CHANGE JPG
// //////////////////
// //////////////////
// $('scenario-change-jpg').addEventListener('click', async () => {
//   let jpgFile = null;
//   while (jpgFile == null) {
//     let url = window.prompt('URL of the jpg file');
//     if (url == null) return;
//     let validURL = await testURL(url);
//     if (validURL) {
//       jpgFile = url;
//     } else {
//       alert('Invalid jpg file');
//     }
//   }

//   DATAS.jpgUrl = jpgFile;
//   $('board').style.backgroundImage = `url(${jpgFile})`;
//   saveScenario();
// });

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
  updateStatus();
}

// //////////////////////////
// //////////////////////////
// ///  GENERATE JSON FILE
// //////////////////////////
// //////////////////////////

// function exportJSON() {
//   let dataStr = 'data:text/json;charset=utf-8,' + encodeURIComponent(JSON.stringify(DATAS));
//   let dlAnchorElem = document.getElementById('download-anchor');
//   dlAnchorElem.setAttribute('href', dataStr);
//   dlAnchorElem.setAttribute('download', DATAS.id + '.wttm');
//   dlAnchorElem.click();
// }
// $('save-btn').addEventListener('click', () => exportJSON());

// function exportCompressedJSON() {
//   let d = {
//     id: DATAS.id,
//     name: DATAS.name,
//     jpgUrl: DATAS.jpgUrl,
//     nbrLaps: DATAS.nbrLaps || 0,
//     stressCards: DATAS.stressCards || 0,
//     wttmCards: DATAS.wttmCards || 0,
//     startingCells: DATAS.startingCells || [],
//     podium: { x: 0, y: 0, a: 0 },
//     weatherCardPos: DATAS.weatherCardPos
//       ? { x: parseInt(DATAS.weatherCardPos.x), y: parseInt(DATAS.weatherCardPos.y) }
//       : { x: 0, y: 0 },
//     corners: DATAS.corners || [],
//     floodedSpaces: DATAS.flooded || [],
//     cells: {},
//   };

//   forEachCell((cellId, cell) => {
//     let infos = DATAS.cells[cellId];
//     d.cells[parseInt(cellId)] = {
//       lane: infos.lane,
//       position: infos.position,
//       x: parseInt(infos.x),
//       y: parseInt(infos.y),
//       a: parseInt(infos.a),
//     };
//   });

//   let dataStr = 'data:text/json;charset=utf-8,' + encodeURIComponent(JSON.stringify(d));
//   let dlAnchorElem2 = document.getElementById('download-anchor2');
//   dlAnchorElem2.setAttribute('href', dataStr);
//   dlAnchorElem2.setAttribute('download', DATAS.id + '-min.wttm');
//   dlAnchorElem2.click();
// }
// $('save-compressed-btn').addEventListener('click', () => exportCompressedJSON());

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

function updateStatus() {
  return;
}

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

        modes[section2][mode2] = false;
        $('main-frame').classList.remove(`${mode2}-${section2}`);
        $(`${mode2}-${section2}`).classList.remove('active');
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
      config.elts.forEach((elt) => addSlot(elt.id, config.eltClass, elt.x, elt.y));
    }
  });
}

function addSlot(id, className, x, y) {
  $('board').insertAdjacentHTML('beforeend', `<div class='wttm-slot ${className}' id='slot-${id}' data-id='${id}'></div>`);
  $(`slot-${id}`).style.left = x + 'px';
  $(`slot-${id}`).style.top = y + 'px';
}

// Click on Board
function onClickBoard(sectionConfig, mode, pos) {
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
      onClickBoard(config, mode, { x, y });
    });
  });
});

$('main-frame').addEventListener('mousemove', (evt) => {
  let wrapper = $('main-frame-wrapper');
  let x = evt.x - wrapper.offsetLeft + wrapper.scrollLeft;
  let y = evt.y - wrapper.offsetTop + wrapper.scrollTop;
  $('hover-indicator').style.left = x + 'px';
  $('hover-indicator').style.top = y + 'px';

  $('cell-indicator-counter').innerHTML = evt.target.classList.contains('wttm-slot') ? evt.target.id : '----';
});

// Add new section
$('add-section').addEventListener('click', () => {
  let id = prompt('ID of the section ?');
  if (id == '') return;

  DATAS.sections.push({
    id,
    name: id,
    modes: ['show', 'add'],
    elts: [],
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
