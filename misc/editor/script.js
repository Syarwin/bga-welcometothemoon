let $ = (id) => document.getElementById(id);
let DATAS = null;

let modes = localStorage.getItem('wttmModes');
modes = modes === null ? {} : JSON.parse(modes);

const EXCLUSIVE_MODES = ['add'];
const ONCLICK_MODES = ['add'];

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
  $('data').innerHTML = JSON.stringify(DATAS);
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

// ///////////////////////////////////////////////
// ///////////////////////////////////////////////
// ///////////////////////////////////////////////
// //  ____            _   _
// // / ___|  ___  ___| |_(_) ___  _ __  ___
// // \___ \ / _ \/ __| __| |/ _ \| '_ \/ __|
// //  ___) |  __/ (__| |_| | (_) | | | \__ \
// // |____/ \___|\___|\__|_|\___/|_| |_|___/
// ///////////////////////////////////////////////
// ///////////////////////////////////////////////
// ///////////////////////////////////////////////

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

  config.modes.forEach((mode) => {
    // HTML
    $(`section-${section}`)
      .querySelector('.toolbar')
      .insertAdjacentHTML('beforeend', `<button id="${mode}-${section}"><i class="fa-icon fa-${icons[mode]}"></i></button>`);

    // Event listener
    $(`${mode}-${section}`).addEventListener('click', () => toggleMode(section, mode));
    toggleMode(section, mode, modes[section][mode] || false);

    // ADD MODE
    if (mode == 'add') {
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

// ///////////////////////////////////////////////////////////////////////////
// //  _____                 _     _   _                 _ _
// // | ____|_   _____ _ __ | |_  | | | | __ _ _ __   __| | | ___ _ __ ___
// // |  _| \ \ / / _ \ '_ \| __| | |_| |/ _` | '_ \ / _` | |/ _ \ '__/ __|
// // | |___ \ V /  __/ | | | |_  |  _  | (_| | | | | (_| | |  __/ |  \__ \
// // |_____| \_/ \___|_| |_|\__| |_| |_|\__,_|_| |_|\__,_|_|\___|_|  |___/
// ///////////////////////////////////////////////////////////////////////////

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
});

// async function promptPosition(className) {
//   $('main-frame').classList.add('hover');
//   $('hover-indicator').className = className;

//   return new Promise((resolve, reject) => {
//     clickCallback = resolve;
//   });
// }

// let highlightedCells = {};
// function highlightCells(cellIds = null, className = null) {
//   if (cellIds != null) {
//     cellIds.forEach(
//       (cellId) => (highlightedCells[cellId] = Array.isArray(className) && className[cellId] ? className[cellId] : className)
//     );
//   }

//   Object.keys(highlightedCells).forEach((cellId) => {
//     let c = highlightedCells[cellId];
//     if (c == 'white') c = '#ffffffaa';
//     if (c == 'green') c = '#00ff00aa';

//     CELLS[cellId].style.fill = c;
//   });
// }

// function clearHighlights() {
//   Object.keys(highlightedCells).forEach((cellId) => {
//     CELLS[cellId].style.fill = 'transparent';
//   });
//   highlightedCells = {};
// }

// function onMouseEnterCell(id, cell) {
//   $('cell-indicator-counter').innerHTML = id;

//   if (selectedCell === null) {
//     // Otherwise, display neighbours and/or neighbours
//     if (modes.neighbours.show && DATAS.computed.neighbours) {
//       let neighbourIds = DATAS.cells[id].neighbours;
//       highlightCells(neighbourIds, 'white');
//     }
//   }

//   if (id === selectedCell) return;

//   let color = cell.style.fill;
//   if (color == 'transparent') color = 'rgb(100,100,100)';
//   cell.style.fill = rgba2hex(color, 'ee');
//   cell.style.strokeWidth = '2';
// }

// function onMouseLeaveCell(id, cell) {
//   $('cell-indicator-counter').innerHTML = '----';
//   if (id === selectedCell) return;

//   cell.style.strokeWidth = '1';
//   let color = rgba2hex(cell.style.fill, 'aa');
//   if (color == '#646464aa') color = 'transparent';
//   cell.style.fill = color;

//   if (selectedCell == null) {
//     clearHighlights();
//   } else {
//     highlightCells();
//   }
// }

// let selectedCell = null;
// function onMouseClickCell(id, cell, evt) {
//   if (modes.lanes.edit) {
//     let newPos = prompt('New position ?');
//     DATAS.cells[id].position = parseInt(newPos);
//     updatePositions();
//     saveScenario();
//     return;
//   }

//   if (modes.lanes.end1) {
//     updateLaneEnd(1, id);
//     return;
//   }
//   if (modes.lanes.end2) {
//     updateLaneEnd(2, id);
//     return;
//   }

//   if (modes.centers.edit) {
//     let wrapper = $('main-frame-wrapper');
//     let x = evt.x - wrapper.offsetLeft + wrapper.scrollLeft - 1,
//       y = evt.y - wrapper.offsetTop + wrapper.scrollTop - 3;
//     DATAS.cells[id].x = x;
//     DATAS.cells[id].y = y;
//     updateCenters();
//     saveScenario();
//     return;
//   }

//   if (modes.directions.swap) {
//     swapDirection(id);
//     saveScenario();
//     return;
//   }

//   if (modes.directions.edit) {
//     if (selectedCell == null) {
//       selectedCell = id;
//       cell.style.fill = 'green';
//     } else {
//       changeDirection(selectedCell, evt);
//       CELLS[selectedCell].style.fill = 'transparent';
//       selectedCell = null;
//       saveScenario();
//       clearHighlights();
//     }
//     return;
//   }

//   if (modes.neighbours.edit) {
//     if (selectedCell == null) {
//       selectedCell = id;
//       cell.style.fill = 'green';
//       highlightCells(DATAS.cells[id].neighbours, 'white');
//     } else if (selectedCell == id) {
//       selectedCell = null;
//       cell.style.fill = 'transparent';
//       clearHighlights();
//     } else {
//       toggleNeighbour(selectedCell, id, cell);
//     }
//     return;
//   }

//   if (modes.flooded.edit) {
//     if (DATAS.flooded === undefined) DATAS.flooded = [];

//     const index = DATAS.flooded.indexOf(id);
//     if (index > -1) {
//       DATAS.flooded.splice(index, 1);
//     } else {
//       DATAS.flooded.push(id);
//     }
//     saveScenario();
//     updateFloodedSpaces();
//     return;
//   }
// }

// //////////////////////////////////////
// //   ____           _
// //  / ___|___ _ __ | |_ ___ _ __ ___
// // | |   / _ \ '_ \| __/ _ \ '__/ __|
// // | |__|  __/ | | | ||  __/ |  \__ \
// //  \____\___|_| |_|\__\___|_|  |___/
// //////////////////////////////////////

// function generateCenters() {
//   if ((DATAS.computed.centers || false) && !confirm('Are you sure you want to overwrite existing centers ?')) {
//     return;
//   }

//   forEachCell((cellId, cell) => {
//     let pos = computeCenterOfCell(cell);
//     DATAS.cells[cellId].x = pos.x;
//     DATAS.cells[cellId].y = pos.y;
//   });

//   DATAS.computed.centers = true;
//   saveScenario();
//   updateCenters();
//   toggleShow('centers', true);
//   console.log('Centers computed');
// }

// function updateCenters() {
//   forEachCell((cellId, cell) => {
//     if (!$(`center-${cellId}`)) {
//       $('centers').insertAdjacentHTML('beforeend', `<div class='center-indicator' id='center-${cellId}'></div>`);
//     }

//     $(`center-${cellId}`).style.left = DATAS.cells[cellId].x - 2 + 'px';
//     $(`center-${cellId}`).style.top = DATAS.cells[cellId].y - 2 + 'px';

//     $(`center-${cellId}`).dataset.lane = DATAS.cells[cellId].lane ?? 0;
//     $(`center-${cellId}`).dataset.position = DATAS.cells[cellId].position ?? 0;

//     $(`center-${cellId}`).dataset.flooded = (DATAS.flooded ?? []).includes(cellId) ? 1 : 0;
//   });
// }

// function computeCenterOfCell(cell) {
//   const M = 40;
//   let pathLength = Math.floor(cell.getTotalLength());
//   let totX = 0,
//     totY = 0;
//   for (let i = 0; i < M; i++) {
//     let pos = cell.getPointAtLength((i * pathLength) / M);
//     totX += pos.x;
//     totY += pos.y;
//   }

//   return {
//     x: totX / M,
//     y: totY / M,
//   };
// }

// ///////////////////////////////////////////////////
// //  ____  _               _   _
// // |  _ \(_)_ __ ___  ___| |_(_) ___  _ __  ___
// // | | | | | '__/ _ \/ __| __| |/ _ \| '_ \/ __|
// // | |_| | | | |  __/ (__| |_| | (_) | | | \__ \
// // |____/|_|_|  \___|\___|\__|_|\___/|_| |_|___/
// ///////////////////////////////////////////////////
// function generateDirections() {
//   if (!DATAS.computed.centers) {
//     alert('You must compute the centers first');
//     return false;
//   }

//   if ((DATAS.computed.directions || false) && !confirm('Are you sure you want to overwrite existing directions ?')) {
//     return;
//   }

//   forEachCell((cellId, cell) => {
//     let center = getCenter(cellId);
//     let angle = computeTangentOfCell(cell, center);
//     DATAS.cells[cellId].a = angle;
//   });

//   DATAS.computed.directions = true;
//   saveScenario();
//   updateDirections();
//   toggleShow('directions', true);
//   console.log('Directions computed');
// }

// function updateDirections() {
//   forEachCell((cellId, cell) => updateDirection(cellId));
// }

// function updateDirection(cellId) {
//   if (!$(`direction-${cellId}`)) {
//     $(`center-${cellId}`).insertAdjacentHTML('beforeend', `<div class='direction-indicator' id='direction-${cellId}'></div>`);
//   }

//   let angle = DATAS.cells[cellId].a ?? 0;
//   $(`direction-${cellId}`).style.transform = `rotate(${angle}deg)`;
// }

// function swapDirection(cellId) {
//   let angle = DATAS.cells[cellId].a;
//   DATAS.cells[cellId].a = (angle + 180) % 360;
//   updateDirection(cellId);
// }

// function changeDirection(id, evt) {
//   let wrapper = $('main-frame-wrapper');
//   let x = evt.clientX - wrapper.offsetLeft + wrapper.scrollLeft - 1,
//     y = evt.clientY - wrapper.offsetTop + wrapper.scrollTop - 3;
//   let center = getCenter(id);
//   console.log(center, x, y);
//   DATAS.cells[id].a = (Math.atan2(y - center.y, x - center.x) * 180) / Math.PI;
//   updateDirection(id);
// }

// function computeTangentOfCell(cell, center) {
//   let pathLength = Math.floor(cell.getTotalLength());
//   const M = 200;
//   let minDist = pathLength * pathLength;
//   let minPos1 = null;
//   let minI = 0;
//   for (let i = 0; i < M; i++) {
//     let pos = cell.getPointAtLength((i * pathLength) / M);
//     let d = dist2(pos, center);
//     if (d < minDist) {
//       minDist = d;
//       minPos1 = pos;
//       minI = i;
//     }
//   }

//   minDist = pathLength * pathLength;
//   let minPos2 = null;
//   for (let i = minI + M / 2 - M / 5; i < minI + M / 2 + M / 5; i++) {
//     let pos = cell.getPointAtLength(((i % M) * pathLength) / M);
//     let d = dist2(pos, center);
//     if (d < minDist) {
//       minDist = d;
//       minPos2 = pos;
//     }
//   }

//   let slope = -(minPos2.x - minPos1.x) / (minPos2.y - minPos1.y);
//   let rotation = (Math.atan2(slope, 1) * 180) / Math.PI;
//   return rotation;
// }

// ///////////////////////////////////////////////////////////
// //  _   _      _       _     _
// // | \ | | ___(_) __ _| |__ | |__   ___  _   _ _ __ ___
// // |  \| |/ _ \ |/ _` | '_ \| '_ \ / _ \| | | | '__/ __|
// // | |\  |  __/ | (_| | | | | |_) | (_) | |_| | |  \__ \
// // |_| \_|\___|_|\__, |_| |_|_.__/ \___/ \__,_|_|  |___/
// //               |___/
// ///////////////////////////////////////////////////////////
// function generateNeighbours() {
//   if (!DATAS.computed.centers || !DATAS.computed.directions) {
//     alert('You must compute the centers and directions first');
//     return false;
//   }

//   if ((DATAS.computed.neighbours || false) && !confirm('Are you sure you want to overwrite existing neighbours ?')) {
//     return;
//   }

//   // Compute neighbours
//   forEachCell((cellId, cell) => {
//     let neighbours = computeNeighbours(cellId);
//     DATAS.cells[cellId].neighbours = neighbours;
//   });

//   DATAS.computed.neighbours = true;
//   saveScenario();
//   toggleShow('neighbours', true);
//   console.log('Neighbours computed');
// }

// function computeNeighbours(id) {
//   let center = getCenter(id);

//   // Keep only the cells within the cone angle
//   let coneAngle = Math.PI / 3;
//   let ids = Object.keys(CELLS).filter((cellId) => {
//     if (cellId == id) return false;
//     let alpha = getAngle(id, cellId);
//     return alpha > -coneAngle && alpha < coneAngle;
//   });

//   // Sort cells by distance
//   let dists = {};
//   ids.forEach((cellId) => {
//     let center2 = getCenter(cellId);
//     dists[cellId] = (center.x - center2.x) * (center.x - center2.x) + (center.y - center2.y) * (center.y - center2.y);
//   });
//   ids = ids.sort(function (id1, id2) {
//     return dists[id1] - dists[id2];
//   });

//   // Keep only the 2 closest ones
//   ids = ids.slice(0, 2);

//   // Keep only the ones close enough
//   let minDist = dists[ids[0]];
//   ids = ids.filter((cellId) => dists[cellId] < 2 * minDist);

//   return ids.map((t) => parseInt(t));
// }

// function toggleNeighbour(selectedCell, id, cell) {
//   let neighbours = DATAS.cells[selectedCell].neighbours;
//   let i = neighbours.findIndex((cId) => cId == id);

//   if (i === -1) {
//     // New neighbour => add it
//     neighbours.push(id);
//     highlightCells([id], 'white');
//   } else {
//     // Already there => remove it
//     neighbours.splice(i, 1);
//     highlightCells([id], 'transparent');
//   }
//   saveScenario();
// }

// ////////////////////////////////
// //  _
// // | |    __ _ _ __   ___  ___
// // | |   / _` | '_ \ / _ \/ __|
// // | |__| (_| | | | |  __/\__ \
// // |_____\__,_|_| |_|\___||___/
// ////////////////////////////////

// // Lane ends
// function updateLaneEnd(lane, cellId) {
//   if (!DATAS.computed.laneEnds) DATAS.computed.laneEnds = {};
//   DATAS.computed.laneEnds[`end${lane}`] = cellId;
//   modes.lanes[`end${lane}`] = false;
//   saveScenario();
//   updateLaneEnds();
//   $(`end${lane}-lanes`).classList.remove('active');
// }

// function updateLaneEnds() {
//   [1, 2].forEach((i) => {
//     let end = `end${i}`;
//     let cellId = DATAS.computed.laneEnds[end] ?? null;
//     if (cellId) {
//       if (!$(`lane-${end}`)) {
//         $(`center-${cellId}`).insertAdjacentHTML(
//           'beforeend',
//           `<div id='lane-${end}' class='lane-end-indicator'>${i}<i class='fa-icon fa-flag'></i></div>`
//         );
//       }

//       $(`center-${cellId}`).insertAdjacentElement('beforeend', $(`lane-${end}`));
//     }
//   });
// }

// // Generate lanes and positions
// function generateLanes() {
//   if (!DATAS.computed.neighbours) {
//     alert('You must compute the neighbours first');
//     return false;
//   }
//   if (!DATAS.computed.laneEnds || !DATAS.computed.laneEnds.end1 || !DATAS.computed.laneEnds.end2) {
//     alert('You must add the lane endings first');
//     return false;
//   }

//   if ((DATAS.computed.lanes || false) && !confirm('Are you sure you want to overwrite existing lanes ?')) {
//     return;
//   }

//   // Compute neighbours
//   let startingCells = [];
//   const startingCellPosition = Object.keys(CELLS).length / 2 - 3;

//   let currentIds = [DATAS.computed.laneEnds['end1'], DATAS.computed.laneEnds['end2']];
//   let lap = Object.keys(CELLS).length / 2;
//   for (let i = 1; i <= lap; i++) {
//     let candidateIds = merge(DATAS.cells[currentIds[0]].neighbours, DATAS.cells[currentIds[1]].neighbours);
//     if (candidateIds.length != 2) {
//       alert('Invalid number of neighbours for cells:' + currentIds[0] + ' and ' + currentIds[1]);
//       break;
//     }

//     let cellId0 = null,
//       cellId1 = null;
//     let c1 = getCenter(currentIds[0]);
//     let c2 = getCenter(currentIds[1]);
//     let c3 = getCenter(candidateIds[0]);
//     let c4 = getCenter(candidateIds[1]);

//     if (intersects(c1, c3, c2, c4)) {
//       cellId0 = candidateIds[1];
//       cellId1 = candidateIds[0];
//     } else {
//       cellId0 = candidateIds[0];
//       cellId1 = candidateIds[1];
//     }

//     DATAS.cells[cellId0].lane = 1;
//     DATAS.cells[cellId1].lane = 2;
//     DATAS.cells[cellId0].position = i;
//     DATAS.cells[cellId1].position = i;
//     currentIds = [cellId0, cellId1];
//     if (i >= startingCellPosition) {
//       startingCells.unshift(cellId1);
//       startingCells.unshift(cellId0);
//     }
//   }

//   DATAS.computed.lanes = true;
//   DATAS.startingCells = startingCells;
//   saveScenario();
//   toggleShow('lanes', true);
//   updateCenters();
//   console.log('Lanes computed');
// }

// //////////////////////////////////////////////
// //  _____ _                 _          _
// // |  ___| | ___   ___   __| | ___  __| |
// // | |_  | |/ _ \ / _ \ / _` |/ _ \/ _` |
// // |  _| | | (_) | (_) | (_| |  __/ (_| |
// // |_|   |_|\___/ \___/ \__,_|\___|\__,_|
// //////////////////////////////////////////////

// function updateFloodedSpaces() {
//   updateCenters();
// }

// ////////////////////////////////////////////
// //    ____
// //   / ___|___  _ __ _ __   ___ _ __ ___
// //  | |   / _ \| '__| '_ \ / _ \ '__/ __|
// //  | |__| (_) | |  | | | |  __/ |  \__ \
// //   \____\___/|_|  |_| |_|\___|_|  |___/
// ////////////////////////////////////////////

// function createCornerEntries() {
//   DATAS.corners.forEach((corner, j) => {
//     $('corners-holder').insertAdjacentHTML(
//       'beforeend',
//       `<tr class='corner-entry'>
//       <td id='corner-pos-${j}'></td>
//       <td id='corner-speed-${j}'></td>
//       <td id='corner-lane-${j}'></td>
//       <td id='corner-legend-${j}'></td>
//       <td id='corner-coordinates-${j}'></td>
//       <td id='corner-tent-${j}'></td>
//       <td id='corner-sector-${j}'></td>
//     </tr>`
//     );
//     $('main-frame').insertAdjacentHTML(
//       'beforeend',
//       `<div class='corner-indicator' id='corner-indicator-${j}'></div>
//      <div class='corner-tent' id='corner-tent-indicator-${j}'></div>
//      <div class='corner-tent' id='corner-sector-indicator-${j}'></div>`
//     );

//     // Add event listeners
//     $(`corner-pos-${j}`).addEventListener('click', () => {
//       let pos = prompt('What is the number on the cell right before the corner?');
//       if (pos == null) return;
//       DATAS.corners[j].position = pos;
//       updateCorners();
//       saveScenario();
//     });

//     $(`corner-speed-${j}`).addEventListener('click', () => {
//       let speed = prompt('What is the max speed?');
//       if (speed == null) return;
//       DATAS.corners[j].speed = speed;
//       updateCorners();
//       saveScenario();
//     });

//     $(`corner-lane-${j}`).addEventListener('click', () => {
//       let lane = prompt('What is the main lane after the corner? (orange = 1, purple = 2)');
//       if (lane == null) return;
//       DATAS.corners[j].lane = lane;
//       updateCorners();
//       saveScenario();
//     });

//     $(`corner-legend-${j}`).addEventListener('click', () => {
//       let legend = prompt('What is number on the cell right before the legend line?');
//       if (legend == null) return;
//       DATAS.corners[j].legend = legend;
//       updateCorners();
//       saveScenario();
//     });

//     $(`corner-coordinates-${j}`).addEventListener('click', async () => {
//       let pos = await promptPosition('corner');
//       DATAS.corners[j].x = pos.x;
//       DATAS.corners[j].y = pos.y;
//       checkChicanes(j);
//       updateCorners();
//       saveScenario();
//     });

//     $(`corner-tent-${j}`).addEventListener('click', async () => {
//       let pos = await promptPosition('tent');
//       DATAS.corners[j].tentX = pos.x;
//       DATAS.corners[j].tentY = pos.y;
//       checkChicanes(j);
//       updateCorners();
//       saveScenario();
//     });

//     $(`corner-sector-${j}`).addEventListener('click', async () => {
//       let pos = await promptPosition('tent');
//       DATAS.corners[j].sectorTentX = pos.x;
//       DATAS.corners[j].sectorTentY = pos.y;
//       updateCorners();
//       saveScenario();
//     });
//   });
// }

// function checkChicanes(j) {
//   if (DATAS.corners === undefined) return;

//   // Modified corners
//   let corner1 = DATAS.corners[j];

//   // Try to find another corner
//   let iCorner = null;
//   DATAS.corners.forEach((corner, i) => {
//     if (i == j) return;
//     if (corner.tentX === undefined || corner.tentX == 0) return;

//     let dist = Math.abs(corner1.tentX - corner.tentX) + Math.abs(corner1.tentY - corner.tentY);
//     if (dist < 50) {
//       iCorner = i;
//     }
//   });

//   if (iCorner === null) {
//     delete corner1.chicane;
//   } else {
//     corner1.chicane = iCorner;
//   }
// }

// function updateCorners() {
//   if (DATAS.corners === undefined) return;

//   DATAS.corners.forEach((corner, j) => {
//     $(`corner-pos-${j}`).innerHTML = corner.position;
//     $(`corner-pos-${j}`).classList.toggle('ok', corner.position != 0);

//     $(`corner-speed-${j}`).innerHTML = corner.speed;
//     $(`corner-speed-${j}`).classList.toggle('ok', corner.speed != 0);

//     $(`corner-lane-${j}`).innerHTML = corner.lane;
//     $(`corner-lane-${j}`).classList.toggle('ok', corner.lane != 0);

//     $(`corner-legend-${j}`).innerHTML = corner.legend;
//     $(`corner-legend-${j}`).classList.toggle('ok', corner.legend != 0);

//     $(`corner-coordinates-${j}`).classList.toggle('ok', corner.x != 0 && corner.y != 0);
//     $(`corner-indicator-${j}`).classList.toggle('ok', corner.x != 0 && corner.y != 0);
//     $(`corner-indicator-${j}`).style.setProperty('--x', corner.x + 'px');
//     $(`corner-indicator-${j}`).style.setProperty('--y', corner.y + 'px');

//     $(`corner-tent-${j}`).classList.toggle('ok', corner.tentX != 0 && corner.tentY != 0);
//     $(`corner-tent-indicator-${j}`).classList.toggle('ok', corner.tentX != 0 && corner.tentY != 0);
//     $(`corner-tent-indicator-${j}`).style.setProperty('--x', corner.tentX + 'px');
//     $(`corner-tent-indicator-${j}`).style.setProperty('--y', corner.tentY + 'px');
//     $(`corner-tent-${j}`).classList.toggle('chicane', corner.chicane !== undefined);
//     $(`corner-tent-indicator-${j}`).classList.toggle('chicane', corner.chicane !== undefined);

//     $(`corner-sector-${j}`).classList.toggle('ok', corner.sectorTentX != 0 && corner.sectorTentY != 0);
//     $(`corner-sector-indicator-${j}`).classList.toggle('ok', corner.sectorTentX != 0 && corner.sectorTentY != 0);
//     $(`corner-sector-indicator-${j}`).style.setProperty('--x', corner.sectorTentX + 'px');
//     $(`corner-sector-indicator-${j}`).style.setProperty('--y', corner.sectorTentY + 'px');
//     $(`corner-sector-${j}`).classList.toggle('chicane', corner.chicane !== undefined);
//   });

//   let cellLeft = $('corners-table').querySelector('td:not(.ok):not(.chicane)');
//   $(`section-corners`).classList.toggle('computed', cellLeft === null);
// }

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
