// =========================================================================
// 1. DATA LAYER: MOCK CENSUS DATA & CLAIMS (LOCAL STORAGE LAYER)
// =========================================================================

const INITIAL_TREES = [
    { id: 1001, specie: 'JacarandÃ¡', age: 25, height: 12.5, status: 'Bueno', address: 'Plaza Armenia, Palermo, CABA', lat: -34.5888, lng: -58.4285 },
    { id: 1002, specie: 'Ceibo', age: 15, height: 6.2, status: 'Bueno', address: 'Av. Sarmiento 2400, Palermo, CABA', lat: -34.5795, lng: -58.4148 },
    { id: 1003, specie: 'Fresno', age: 40, height: 16.0, status: 'Regular', address: 'Defensa 850, San Telmo, CABA', lat: -34.6178, lng: -58.3712 },
    { id: 1004, specie: 'Palo Borracho', age: 30, height: 14.2, status: 'Malo', address: 'Plaza Francia, Recoleta, CABA', lat: -34.5835, lng: -58.3927 },
    { id: 1005, specie: 'Tilo', age: 12, height: 5.8, status: 'Bueno', address: 'Juramento 1900, Belgrano, CABA', lat: -34.5615, lng: -58.4552 },
    { id: 1006, specie: 'LiquidÃ¡mbar', age: 18, height: 11.0, status: 'Bueno', address: 'Av. Del Libertador 3200, Palermo, CABA', lat: -34.5768, lng: -58.4063 },
    { id: 1007, specie: 'JacarandÃ¡', age: 35, height: 15.2, status: 'Regular', address: 'Plaza Cortazar, Palermo, CABA', lat: -34.5915, lng: -58.4307 },
    { id: 1008, specie: 'Fresno', age: 8, height: 4.5, status: 'Bueno', address: 'Av. Cabildo 2100, Belgrano, CABA', lat: -34.5630, lng: -58.4568 },
    { id: 1009, specie: 'Tilo', age: 50, height: 18.5, status: 'Malo', address: 'BolÃ­var 600, San Telmo, CABA', lat: -34.6190, lng: -58.3735 },
    { id: 1010, specie: 'Ceibo', age: 22, height: 8.0, status: 'Regular', address: 'Parque Rivadavia, Caballito, CABA', lat: -34.6185, lng: -58.4358 },
    { id: 1011, specie: 'LiquidÃ¡mbar', age: 7, height: 4.8, status: 'Bueno', address: 'Juana Manso 1100, Puerto Madero, CABA', lat: -34.6120, lng: -58.3615 },
    { id: 1012, specie: 'Palo Borracho', age: 45, height: 17.5, status: 'Bueno', address: 'Av. 9 de Julio 1200, San NicolÃ¡s, CABA', lat: -34.6062, lng: -58.3816 }
];

const INITIAL_CLAIMS = [
    {
        id: 'REC-1024',
        treeId: 1004,
        neighborName: 'Juan PÃ©rez',
        neighborEmail: 'juan.perez@gmail.com',
        neighborPhone: '11 5555 1234',
        issueType: 'ExtracciÃ³n total',
        description: 'El Ã¡rbol de Palo Borracho tiene el tronco inclinado y peligro de caÃ­da sobre el sendero de peatones.',
        date: '2026-06-01T10:30:00-03:00',
        status: 'En curso',
        notes: 'Inspector evaluÃ³ riesgo medio-alto. Se planificÃ³ corte preventivo de ramas superiores para el dÃ­a 12/06/2026.'
    },
    {
        id: 'REC-3012',
        treeId: 1003,
        neighborName: 'MarÃ­a RodrÃ­guez',
        neighborEmail: 'maria.rod@hotmail.com',
        neighborPhone: '11 6666 4321',
        issueType: 'Vereda rota',
        description: 'Las raÃ­ces del fresno rompieron completamente las baldosas de la vereda, lo que dificulta el paso de cochecitos.',
        date: '2026-06-03T15:45:00-03:00',
        status: 'Pendiente',
        notes: 'Reclamo recibido. En cola de asignaciÃ³n a cuadrilla de reparaciÃ³n de veredas.'
    },
    {
        id: 'REC-8521',
        treeId: 1009,
        neighborName: 'Carlos GÃ³mez',
        neighborEmail: 'cgomez@outlook.com',
        neighborPhone: '11 4444 8888',
        issueType: 'Ãrbol enfermo',
        description: 'El tilo estÃ¡ perdiendo las hojas antes de tiempo y tiene una pelusa blanca en las ramas (plaga).',
        date: '2026-06-05T09:15:00-03:00',
        status: 'Resuelto',
        notes: 'Se aplicÃ³ tratamiento fitosanitario de pulverizaciÃ³n orgÃ¡nica contra cochinilla algodonosa. El Ã¡rbol muestra mejorÃ­a.'
    }
];

const TREE_IMAGES = {
    'JacarandÃ¡': 'https://images.unsplash.com/photo-1616781297592-fb2721868350?auto=format&fit=crop&w=600&q=80',
    'Ceibo': 'https://images.unsplash.com/photo-1598902108854-10e335adac99?auto=format&fit=crop&w=600&q=80',
    'Fresno': 'https://images.unsplash.com/photo-1502082553048-f009c37129b9?auto=format&fit=crop&w=600&q=80',
    'Palo Borracho': 'https://images.unsplash.com/photo-1613967193442-19cfb77fdef5?auto=format&fit=crop&w=600&q=80',
    'Tilo': 'https://images.unsplash.com/photo-1473448912268-2022ce9509d8?auto=format&fit=crop&w=600&q=80',
    'LiquidÃ¡mbar': 'https://images.unsplash.com/photo-1507499739999-097706ad8914?auto=format&fit=crop&w=600&q=80',
    'Default': 'https://images.unsplash.com/photo-1502082553048-f009c37129b9?auto=format&fit=crop&w=600&q=80'
};

class DataStorage {
    static init() {
        if (!localStorage.getItem('arbolado_trees')) {
            localStorage.setItem('arbolado_trees', JSON.stringify(INITIAL_TREES));
        }
        if (!localStorage.getItem('arbolado_claims')) {
            localStorage.setItem('arbolado_claims', JSON.stringify(INITIAL_CLAIMS));
        }
    }

    static getTrees() {
        return JSON.parse(localStorage.getItem('arbolado_trees')) || [];
    }

    static saveTrees(trees) {
        localStorage.setItem('arbolado_trees', JSON.stringify(trees));
    }

    static getClaims() {
        return JSON.parse(localStorage.getItem('arbolado_claims')) || [];
    }

    static saveClaims(claims) {
        localStorage.setItem('arbolado_claims', JSON.stringify(claims));
    }
}

// =========================================================================
// 2. SPA ROUTER
// =========================================================================

const STATE = {
    currentView: 'welcome',
    currentUser: null, // 'public', 'admin', null
    activeTree: null, // clicked tree object
    mapInstance: null,
    mapMarkers: [],
    adminMapInstance: null,
    adminMapMarkers: [],
    claimCreationMap: null, // Mapa para crear reclamo
    selectedClaimCoordinates: null // Coordenadas seleccionadas en el mapa de reclamos
};

function navigateTo(viewId) {
    // Hide all views
    document.querySelectorAll('.spa-view').forEach(view => {
        view.classList.remove('active');
    });

    // Show target view
    const target = document.getElementById(`view-${viewId}`);
    if (target) {
        target.classList.add('active');
        STATE.currentView = viewId;
    }

    // Adjust navigation items based on active view and user role
    updateNavBar();

    // Trigger view-specific behaviors
    if (viewId === 'map') {
        // Asegura que el contenedor tenga altura antes de inicializar
        const mapContainer = document.getElementById('map');
        const mapLayout = document.querySelector('.map-layout');
        
        console.log('ðŸ“ Map layout visible:', mapLayout?.offsetHeight, 'x', mapLayout?.offsetWidth);
        console.log('ðŸ“ Map container visible:', mapContainer?.offsetHeight, 'x', mapContainer?.offsetWidth);
        
        setTimeout(() => {
            console.log('ðŸ”„ Iniciando mapa despuÃ©s de render...');
            if (mapContainer?.offsetHeight === 0) {
                console.warn('âš ï¸ ALERTA: El contenedor del mapa tiene altura 0!');
                // Fuerza una altura mÃ­nima
                mapContainer.style.minHeight = '600px';
            }
            initMainMap();
            setTimeout(() => {
                if (STATE.mapInstance) {
                    STATE.mapInstance.invalidateSize(true);
                    console.log('âœ“ TamaÃ±o del mapa recalculado');
                }
            }, 300);
        }, 200);
    } else if (viewId === 'create-claim') {
        // Inicializar mapa para crear reclamo
        setTimeout(() => {
            initClaimCreationMap();
        }, 200);
    } else if (viewId === 'admin') {
        if (STATE.currentUser !== 'admin') {
            navigateTo('login');
            return;
        }
        renderAdminDashboard();
    }
}

function updateNavBar() {
    const loginBtn = document.getElementById('link-login');

    if (STATE.currentUser) {
        loginBtn.textContent = 'LOG OUT';
        loginBtn.classList.remove('nav-btn-highlight');
        loginBtn.classList.add('nav-btn-logout');
    } else {
        loginBtn.textContent = 'LOG IN';
        loginBtn.classList.remove('nav-btn-logout');
        loginBtn.classList.add('nav-btn-highlight');
    }
}

function handleLogOut() {
    STATE.currentUser = null;
    STATE.activeTree = null;
    navigateTo('welcome');
}

// =========================================================================
// 3. MAP OPERATIONS (LEAFLET INTEGRATION)
// =========================================================================

// Helper to create custom marker HTML
function createCustomMarkerIcon(status) {
    let colorClass = 'green';
    if (status === 'Regular') colorClass = 'orange';
    if (status === 'Malo') colorClass = 'red';

    return L.divIcon({
        className: 'custom-tree-marker',
        html: `
            <div class="marker-pin ${colorClass}">
                <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <path d="M 50 85 L 50 60" stroke="#ffffff" stroke-width="8" stroke-linecap="round"/>
                    <path d="M 50 60 C 25 60, 20 40, 50 15 C 80 40, 75 60, 50 60 Z" fill="#ffffff"/>
                </svg>
            </div>
            <div class="marker-shadow"></div>
        `,
        iconSize: [36, 42],
        iconAnchor: [18, 42]
    });
}

function initMainMap() {
    console.log('Iniciando mapa...', STATE.mapInstance);
    
    if (STATE.mapInstance) {
        console.log('Mapa ya existe, invalidando tamaÃ±o...');
        STATE.mapInstance.invalidateSize(true);
        return;
    }

    // Initialize Leaflet Map (Centered in Buenos Aires)
    console.log('Creando nueva instancia de Leaflet...');
    const mapContainer = document.getElementById('map');
    console.log('Contenedor del mapa:', mapContainer, 'TamaÃ±o:', mapContainer?.offsetWidth, 'x', mapContainer?.offsetHeight);
    
    try {
        STATE.mapInstance = L.map('map').setView([-34.5888, -58.4285], 13);
        console.log('Mapa creado:', STATE.mapInstance);

        // Correct OpenStreetMap Tile URL Layer (Fixed URL percent encoding issue)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(STATE.mapInstance);
        console.log('Tileset agregado.');

        // Fuerza recalcular el tamaÃ±o del contenedor
        setTimeout(() => {
            STATE.mapInstance.invalidateSize(true);
            console.log('Map size invalidated');
        }, 500);

        // Load markers
        renderMapMarkers();
    } catch (error) {
        console.error('Error al inicializar el mapa:', error);
    }
}

function initClaimCreationMap() {
    console.log('Inicializando mapa para crear reclamo...');
    
    if (STATE.claimCreationMap) {
        STATE.claimCreationMap.invalidateSize(true);
        return;
    }

    try {
        // Crear mapa para crear reclamo
        STATE.claimCreationMap = L.map('claim-creation-map').setView([-34.5888, -58.4285], 13);
        
        // Agregar tileset
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(STATE.claimCreationMap);

        // Recalcular tamaÃ±o
        setTimeout(() => {
            STATE.claimCreationMap.invalidateSize(true);
        }, 300);

        // Event listener para clicks en el mapa
        STATE.claimCreationMap.on('click', handleClaimMapClick);
        
        console.log('âœ“ Mapa de crear reclamo inicializado');
    } catch (error) {
        console.error('Error al inicializar mapa de reclamo:', error);
    }
}

function handleClaimMapClick(e) {
    const lat = e.latlng.lat.toFixed(6);
    const lng = e.latlng.lng.toFixed(6);
    
    STATE.selectedClaimCoordinates = { lat: parseFloat(lat), lng: parseFloat(lng) };
    
    // Actualizar display de coordenadas
    document.getElementById('claim-selected-coords').textContent = `${lat}, ${lng}`;
    
    // Remover marcador anterior si existe
    document.querySelectorAll('.claim-marker').forEach(m => m.remove());
    
    // Agregar nuevo marcador
    L.marker([lat, lng], {
        icon: L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        })
    }).addTo(STATE.claimCreationMap).bindPopup('UbicaciÃ³n seleccionada');
    
    console.log('âœ“ UbicaciÃ³n seleccionada:', lat, lng);
}

function renderMapMarkers() {
    if (!STATE.mapInstance) return;

    // Clear existing markers
    STATE.mapMarkers.forEach(marker => STATE.mapInstance.removeLayer(marker));
    STATE.mapMarkers = [];

    const trees = DataStorage.getTrees();
    const claims = DataStorage.getClaims();

    // Gather active filters
    const specieFilter = document.getElementById('filter-especie').value;
    const alturaFilter = document.getElementById('filter-altura').value;
    const edadFilter = document.getElementById('filter-edad').value;
    const estadoFilter = document.getElementById('filter-estado').value;

    trees.forEach(tree => {
        // Apply species filter
        if (specieFilter && tree.specie !== specieFilter) return;

        // Apply height filter
        if (alturaFilter) {
            if (alturaFilter === 'bajo' && tree.height >= 6) return;
            if (alturaFilter === 'medio' && (tree.height < 6 || tree.height > 12)) return;
            if (alturaFilter === 'alto' && tree.height <= 12) return;
        }

        // Apply age filter
        if (edadFilter) {
            if (edadFilter === 'joven' && tree.age >= 10) return;
            if (edadFilter === 'maduro' && (tree.age < 10 || tree.age > 30)) return;
            if (edadFilter === 'centenario' && tree.age <= 30) return;
        }

        // Apply health status filter
        if (estadoFilter && tree.status !== estadoFilter) return;

        // Determine if tree has active claims
        const treeClaims = claims.filter(c => c.treeId === tree.id && c.status !== 'Resuelto');
        const effectiveStatus = treeClaims.length > 0 ? 'Malo' : tree.status;

        // Create marker
        const marker = L.marker([tree.lat, tree.lng], {
            icon: createCustomMarkerIcon(effectiveStatus)
        });

        // Click handler to open sidebar tree details
        marker.on('click', () => {
            showTreeDetails(tree);
        });

        marker.addTo(STATE.mapInstance);
        STATE.mapMarkers.push(marker);
    });
}

function showTreeDetails(tree) {
    STATE.activeTree = tree;

    // Fill in Details Card
    document.getElementById('tree-detail-id-badge').textContent = `ID: #${tree.id}`;
    document.getElementById('tree-detail-specie').textContent = tree.specie;
    document.getElementById('tree-detail-edad').textContent = `${tree.age} aÃ±os`;
    document.getElementById('tree-detail-altura').textContent = `${tree.height} m`;
    document.getElementById('tree-detail-direccion').textContent = tree.address;

    // Image mapping
    const imgEl = document.getElementById('tree-detail-img');
    imgEl.src = TREE_IMAGES[tree.specie] || TREE_IMAGES['Default'];
    imgEl.alt = `Foto de ${tree.specie}`;

    // Status Pill
    const statusPill = document.getElementById('tree-detail-estado');
    statusPill.textContent = tree.status;
    statusPill.className = 'status-pill'; // Reset classes
    if (tree.status === 'Bueno') statusPill.classList.add('good');
    if (tree.status === 'Regular') statusPill.classList.add('regular');
    if (tree.status === 'Malo') statusPill.classList.add('bad');

    // Active claims warnings
    const claims = DataStorage.getClaims();
    const treeClaims = claims.filter(c => c.treeId === tree.id && c.status !== 'Resuelto');
    const claimWarningBox = document.getElementById('tree-active-claims-box');
    if (treeClaims.length > 0) {
        claimWarningBox.style.display = 'block';
    } else {
        claimWarningBox.style.display = 'none';
    }

    // Switch panels in sidebar
    switchSidebarPanel('tree');

    // Pan map to clicked tree
    if (STATE.mapInstance) {
        STATE.mapInstance.flyTo([tree.lat, tree.lng], 16, { animate: true, duration: 1.0 });
    }
}

function switchSidebarPanel(panelName) {
    document.querySelectorAll('.sidebar-panel').forEach(panel => {
        panel.classList.remove('active');
    });

    const target = document.getElementById(`sidebar-panel-${panelName}`);
    if (target) {
        target.classList.add('active');
    }

    // Auto-open sidebar if it was collapsed
    const sidebar = document.getElementById('map-sidebar');
    if (sidebar.classList.contains('collapsed')) {
        sidebar.classList.remove('collapsed');
    }
}

// Search address location lookup
function searchAddress() {
    const query = document.getElementById('map-search-input').value.toLowerCase().trim();
    if (!query) return;

    const trees = DataStorage.getTrees();

    // Find tree matching the address or species
    const matchedTree = trees.find(t =>
        t.address.toLowerCase().includes(query) ||
        t.specie.toLowerCase().includes(query)
    );

    if (matchedTree) {
        showTreeDetails(matchedTree);
    } else {
        alert('DirecciÃ³n o especie no encontrada en el censo. Intenta buscar "Palermo", "Belgrano" o una especie como "JacarandÃ¡".');
    }
}

// =========================================================================
// 4. PUBLIC CLAIMS & TRACKING TIMELINE
// =========================================================================

function generateTrackingCode() {
    const rand = Math.floor(1000 + Math.random() * 9000);
    return `REC-${rand}`;
}

function submitClaimForm(e) {
    e.preventDefault();
    if (!STATE.activeTree) return;

    const neighborName = document.getElementById('claim-neighbor-name').value;
    const neighborEmail = document.getElementById('claim-neighbor-email').value;
    const neighborPhone = document.getElementById('claim-neighbor-phone').value;
    const issueType = document.getElementById('claim-issue-type').value;
    const description = document.getElementById('claim-description').value;

    const trackingCode = generateTrackingCode();
    const newClaim = {
        id: trackingCode,
        treeId: STATE.activeTree.id,
        neighborName,
        neighborEmail,
        neighborPhone,
        issueType,
        description,
        date: new Date().toISOString(),
        status: 'Pendiente',
        notes: 'Reclamo ingresado. Se programarÃ¡ inspecciÃ³n tÃ©cnica de campo en breve.'
    };

    const claims = DataStorage.getClaims();
    claims.push(newClaim);
    DataStorage.saveClaims(claims);

    // Show success details
    document.getElementById('claim-success-code').textContent = trackingCode;
    switchSidebarPanel('claim-success');

    // Reset Form
    document.getElementById('tree-claim-form').reset();

    // Refresh map markers to reflect any new claims status
    renderMapMarkers();
}

function searchClaimTracking() {
    const codeInput = document.getElementById('tracking-input-code').value.toUpperCase().trim();
    const errorAlert = document.getElementById('tracking-error-msg');
    const resultsCard = document.getElementById('tracking-results-card');

    if (!codeInput) return;

    const claims = DataStorage.getClaims();
    const claim = claims.find(c => c.id === codeInput);

    if (!claim) {
        errorAlert.style.display = 'block';
        resultsCard.style.display = 'none';
        return;
    }

    errorAlert.style.display = 'none';
    resultsCard.style.display = 'block';

    // Populate Claim Meta Info
    document.getElementById('track-result-code').textContent = claim.id;

    const trees = DataStorage.getTrees();
    const tree = trees.find(t => t.id === claim.treeId);
    document.getElementById('track-result-tree-info').textContent = tree ? `#${tree.id} (${tree.specie})` : `#${claim.treeId}`;

    document.getElementById('track-result-type').textContent = claim.issueType;

    const regDate = new Date(claim.date);
    document.getElementById('track-result-date').textContent = regDate.toLocaleDateString('es-AR', { day: '2-digit', month: '2-digit', year: 'numeric' });

    // Populate notes
    document.getElementById('track-result-notes').textContent = claim.notes || 'No hay comentarios del inspector aÃºn.';

    // Populate Timeline Steps Classes
    const stepRegistrado = document.getElementById('step-registrado');
    const stepRevision = document.getElementById('step-revision');
    const stepCurso = document.getElementById('step-curso');
    const stepResuelto = document.getElementById('step-resuelto');

    // Clear previous classes
    [stepRegistrado, stepRevision, stepCurso, stepResuelto].forEach(step => {
        step.className = 'timeline-step';
    });

    // Step dates placeholders
    const date1 = document.getElementById('track-step-date-1');
    const date2 = document.getElementById('track-step-date-2');
    const date3 = document.getElementById('track-step-date-3');
    const date4 = document.getElementById('track-step-date-4');

    date1.textContent = regDate.toLocaleDateString('es-AR');
    date2.textContent = 'Pendiente';
    date3.textContent = 'Pendiente';
    date4.textContent = 'Pendiente';

    // Status timeline routing
    if (claim.status === 'Pendiente') {
        stepRegistrado.classList.add('active');
    } else if (claim.status === 'En curso') {
        stepRegistrado.classList.add('completed');
        stepRevision.classList.add('completed');
        stepCurso.classList.add('active');

        // Estimate mock dates for middle steps
        const mockRevisionDate = new Date(regDate.getTime() + 24 * 60 * 60 * 1000); // +1 day
        date2.textContent = mockRevisionDate.toLocaleDateString('es-AR');
        date3.textContent = 'En Proceso';
    } else if (claim.status === 'Resuelto') {
        stepRegistrado.classList.add('completed');
        stepRevision.classList.add('completed');
        stepCurso.classList.add('completed');
        stepResuelto.classList.add('completed');

        const mockRevisionDate = new Date(regDate.getTime() + 24 * 60 * 60 * 1000); // +1 day
        const mockWorkingDate = new Date(regDate.getTime() + 3 * 24 * 60 * 60 * 1000); // +3 days
        const mockResolvedDate = new Date(regDate.getTime() + 5 * 24 * 60 * 60 * 1000); // +5 days

        date2.textContent = mockRevisionDate.toLocaleDateString('es-AR');
        date3.textContent = mockWorkingDate.toLocaleDateString('es-AR');
        date4.textContent = mockResolvedDate.toLocaleDateString('es-AR');
    }
}

// =========================================================================
// 5. INSPECTOR / ADMIN PORTAL LOGIC
// =========================================================================

let excelSortConfig = { key: 'id', direction: 'asc' };

function renderAdminDashboard() {
    // 1. Update claims count badge
    const claims = DataStorage.getClaims();
    const pendingClaims = claims.filter(c => c.status === 'Pendiente').length;
    document.getElementById('admin-unread-claims-count').textContent = pendingClaims;

    // 2. Populate filters in spreadsheet toolbar
    populateExcelFilterSpecies();

    // 3. Render Excel Census Table
    renderCensusExcelTable();

    // 4. Render Claims Inbox List
    renderClaimsInboxTable();
}

function populateExcelFilterSpecies() {
    const trees = DataStorage.getTrees();
    const select = document.getElementById('excel-filter-specie');

    // Preserve "Todas las especies"
    select.innerHTML = '<option value="">Todas las especies</option>';

    const species = [...new Set(trees.map(t => t.specie))];
    species.forEach(sp => {
        const opt = document.createElement('option');
        opt.value = sp;
        opt.textContent = sp;
        select.appendChild(opt);
    });
}

function renderCensusExcelTable() {
    const tbody = document.getElementById('census-table-body');
    tbody.innerHTML = '';

    let trees = DataStorage.getTrees();
    const searchVal = document.getElementById('excel-search-input').value.toLowerCase().trim();
    const filterSpecie = document.getElementById('excel-filter-specie').value;
    const filterEstado = document.getElementById('excel-filter-estado').value;

    // Apply spreadsheet filtering
    trees = trees.filter(tree => {
        if (filterSpecie && tree.specie !== filterSpecie) return false;
        if (filterEstado && tree.status !== filterEstado) return false;

        if (searchVal) {
            const matchesId = tree.id.toString().includes(searchVal);
            const matchesSpecie = tree.specie.toLowerCase().includes(searchVal);
            const matchesAddress = tree.address.toLowerCase().includes(searchVal);
            if (!matchesId && !matchesSpecie && !matchesAddress) return false;
        }
        return true;
    });

    // Apply sorting
    trees.sort((a, b) => {
        let valA = a[excelSortConfig.key];
        let valB = b[excelSortConfig.key];

        if (typeof valA === 'string') {
            valA = valA.toLowerCase();
            valB = valB.toLowerCase();
        }

        if (valA < valB) return excelSortConfig.direction === 'asc' ? -1 : 1;
        if (valA > valB) return excelSortConfig.direction === 'asc' ? 1 : -1;
        return 0;
    });

    if (trees.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; color: var(--color-text-gray);">No se encontraron Ã¡rboles en el censo.</td></tr>';
        return;
    }

    trees.forEach(tree => {
        const tr = document.createElement('tr');

        let statusClass = 'good';
        if (tree.status === 'Regular') statusClass = 'regular';
        if (tree.status === 'Malo') statusClass = 'bad';

        tr.innerHTML = `
            <td><strong>#${tree.id}</strong></td>
            <td>${tree.specie}</td>
            <td>${tree.age} aÃ±os</td>
            <td>${tree.height} m</td>
            <td><span class="status-pill ${statusClass}">${tree.status}</span></td>
            <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;" title="${tree.address}">${tree.address}</td>
            <td style="font-family: monospace; font-size: 12px; color: var(--color-text-gray);">${tree.lat.toFixed(5)}, ${tree.lng.toFixed(5)}</td>
            <td>
                <button class="btn-table-action btn-table-action-edit" data-id="${tree.id}">Editar</button>
                <button class="btn-table-action btn-table-action-delete" data-id="${tree.id}">Eliminar</button>
            </td>
        `;

        // Attach event handlers
        tr.querySelector('.btn-table-action-edit').addEventListener('click', () => openTreeModal(tree));
        tr.querySelector('.btn-table-action-delete').addEventListener('click', () => deleteTree(tree.id));

        tbody.appendChild(tr);
    });
}

function renderClaimsInboxTable() {
    const tbody = document.getElementById('claims-table-body');
    tbody.innerHTML = '';

    const claims = DataStorage.getClaims();
    const trees = DataStorage.getTrees();

    if (claims.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; color: var(--color-text-gray);">No hay reclamos cargados en la bandeja.</td></tr>';
        return;
    }

    // Sort claims: Pending first, then date descending
    const sortedClaims = [...claims].sort((a, b) => {
        if (a.status === 'Pendiente' && b.status !== 'Pendiente') return -1;
        if (a.status !== 'Pendiente' && b.status === 'Pendiente') return 1;
        return new Date(b.date) - new Date(a.date);
    });

    sortedClaims.forEach(claim => {
        const tree = trees.find(t => t.id === claim.treeId);
        const tr = document.createElement('tr');

        let statusClass = 'pendiente';
        if (claim.status === 'En curso') statusClass = 'encurso';
        if (claim.status === 'Resuelto') statusClass = 'resuelto';

        const cDate = new Date(claim.date).toLocaleDateString('es-AR');

        tr.innerHTML = `
            <td><strong>${claim.id}</strong></td>
            <td>#${claim.treeId}</td>
            <td>${tree ? tree.specie : '--'}</td>
            <td>${claim.neighborName}</td>
            <td>${claim.issueType}</td>
            <td>${cDate}</td>
            <td><span class="badge-claim-status ${statusClass}">${claim.status}</span></td>
            <td>
                <button class="btn-table-action btn-table-action-manage" data-id="${claim.id}">Ver y Atender</button>
            </td>
        `;

        tr.querySelector('.btn-table-action-manage').addEventListener('click', () => openClaimManageModal(claim));
        tbody.appendChild(tr);
    });
}

// Tree Modal Edit/Create
function openTreeModal(tree = null) {
    const modal = document.getElementById('modal-tree-form');
    const title = document.getElementById('modal-tree-title');
    const form = document.getElementById('admin-tree-form');

    form.reset();

    if (tree) {
        title.textContent = `Editar Ãrbol #${tree.id}`;
        document.getElementById('form-tree-id').value = tree.id;
        document.getElementById('form-tree-specie').value = tree.specie;
        document.getElementById('form-tree-status').value = tree.status;
        document.getElementById('form-tree-age').value = tree.age;
        document.getElementById('form-tree-height').value = tree.height;
        document.getElementById('form-tree-address').value = tree.address;
        document.getElementById('form-tree-lat').value = tree.lat;
        document.getElementById('form-tree-lng').value = tree.lng;
    } else {
        title.textContent = 'Agregar Nuevo Ãrbol al Censo';
        document.getElementById('form-tree-id').value = '';

        // Seed coordinates near central map if possible
        if (STATE.mapInstance) {
            const center = STATE.mapInstance.getCenter();
            document.getElementById('form-tree-lat').value = center.lat.toFixed(6);
            document.getElementById('form-tree-lng').value = center.lng.toFixed(6);
        } else {
            document.getElementById('form-tree-lat').value = '-34.588800';
            document.getElementById('form-tree-lng').value = '-58.428500';
        }
    }

    modal.classList.add('active');
}

function submitTreeForm(e) {
    e.preventDefault();

    const treeIdInput = document.getElementById('form-tree-id').value;
    const specie = document.getElementById('form-tree-specie').value.trim();
    const status = document.getElementById('form-tree-status').value;
    const age = parseInt(document.getElementById('form-tree-age').value);
    const height = parseFloat(document.getElementById('form-tree-height').value);
    const address = document.getElementById('form-tree-address').value.trim();
    const lat = parseFloat(document.getElementById('form-tree-lat').value);
    const lng = parseFloat(document.getElementById('form-tree-lng').value);

    let trees = DataStorage.getTrees();

    if (treeIdInput) {
        // Edit mode
        const treeId = parseInt(treeIdInput);
        trees = trees.map(t => {
            if (t.id === treeId) {
                return { ...t, specie, status, age, height, address, lat, lng };
            }
            return t;
        });
    } else {
        // Create mode
        const newId = trees.length > 0 ? Math.max(...trees.map(t => t.id)) + 1 : 1001;
        const newTree = { id: newId, specie, age, height, status, address, lat, lng };
        trees.push(newTree);
    }

    DataStorage.saveTrees(trees);
    document.getElementById('modal-tree-form').classList.remove('active');

    // Refresh admin tables
    renderCensusExcelTable();

    // Re-render main map markers in background
    renderMapMarkers();
}

function deleteTree(treeId) {
    if (!confirm(`Â¿EstÃ¡s seguro de que deseas eliminar el Ã¡rbol #${treeId} del censo general?`)) return;

    let trees = DataStorage.getTrees();
    trees = trees.filter(t => t.id !== treeId);
    DataStorage.saveTrees(trees);

    renderCensusExcelTable();
    renderMapMarkers();
}

// Claim Manage Modal
function openClaimManageModal(claim) {
    const modal = document.getElementById('modal-claim-manage');
    document.getElementById('modal-claim-code-badge').textContent = claim.id;
    document.getElementById('form-claim-id').value = claim.id;

    document.getElementById('detail-claim-neighbor').textContent = claim.neighborName;
    document.getElementById('detail-claim-contact').textContent = `${claim.neighborEmail} / ${claim.neighborPhone}`;
    document.getElementById('detail-claim-tree-id').textContent = `#${claim.treeId}`;

    const trees = DataStorage.getTrees();
    const tree = trees.find(t => t.id === claim.treeId);
    document.getElementById('detail-claim-address').textContent = tree ? tree.address : 'Desconocida';

    document.getElementById('detail-claim-desc').textContent = claim.description;

    document.getElementById('form-claim-status').value = claim.status;
    document.getElementById('form-claim-notes').value = claim.notes || '';

    modal.classList.add('active');
}

function submitClaimUpdateForm(e) {
    e.preventDefault();

    const claimId = document.getElementById('form-claim-id').value;
    const status = document.getElementById('form-claim-status').value;
    const notes = document.getElementById('form-claim-notes').value.trim();

    let claims = DataStorage.getClaims();
    claims = claims.map(c => {
        if (c.id === claimId) {
            return { ...c, status, notes };
        }
        return c;
    });

    DataStorage.saveClaims(claims);
    document.getElementById('modal-claim-manage').classList.remove('active');

    // Refresh admin
    renderAdminDashboard();

    // Refresh map markers (since marker colors could change if claims are resolved)
    renderMapMarkers();
}

// Excel Export to CSV Function
function exportToCSV() {
    let trees = DataStorage.getTrees();
    const searchVal = document.getElementById('excel-search-input').value.toLowerCase().trim();
    const filterSpecie = document.getElementById('excel-filter-specie').value;
    const filterEstado = document.getElementById('excel-filter-estado').value;

    // Apply same filters as visible in spreadsheet
    trees = trees.filter(tree => {
        if (filterSpecie && tree.specie !== filterSpecie) return false;
        if (filterEstado && tree.status !== filterEstado) return false;
        if (searchVal) {
            const matchesId = tree.id.toString().includes(searchVal);
            const matchesSpecie = tree.specie.toLowerCase().includes(searchVal);
            const matchesAddress = tree.address.toLowerCase().includes(searchVal);
            if (!matchesId && !matchesSpecie && !matchesAddress) return false;
        }
        return true;
    });

    // Generate CSV contents
    let csvContent = '\uFEFF'; // Add BOM for Excel UTF-8 encoding support
    csvContent += 'ID;Especie;Edad (AÃ±os);Altura (m);Estado Fitosanitario;DirecciÃ³n;Latitud;Longitud\r\n';

    trees.forEach(t => {
        const row = [
            t.id,
            `"${t.specie}"`,
            t.age,
            t.height.toString().replace('.', ','),
            `"${t.status}"`,
            `"${t.address}"`,
            t.lat.toString().replace('.', ','),
            t.lng.toString().replace('.', ',')
        ];
        csvContent += row.join(';') + '\r\n';
    });

    // Trigger download
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `censo_arbolado_${new Date().toISOString().slice(0, 10)}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// =========================================================================
// 6. EVENT BINDING & APP INITIALIZATION
// =========================================================================

document.addEventListener('DOMContentLoaded', () => {
    // Initialize Local Storage Seeding
    DataStorage.init();
    
    // Verificar que Leaflet estÃ© disponible
    if (typeof L === 'undefined') {
        console.error('Leaflet no estÃ¡ cargado. Verifica que la librerÃ­a estÃ© disponible.');
    } else {
        console.log('âœ“ Leaflet cargado correctamente');
    }
});

// ----------------------------------------------------
// Navigation / Router Event Bindings
// ----------------------------------------------------
document.getElementById('nav-logo').addEventListener('click', () => navigateTo('welcome'));
document.getElementById('link-contacto').addEventListener('click', () => {
    document.getElementById('modal-contacto').classList.add('active');
});
document.getElementById('link-reclamos').addEventListener('click', () => {
    // Reset tracking fields
    document.getElementById('tracking-input-code').value = '';
    document.getElementById('tracking-error-msg').style.display = 'none';
    document.getElementById('tracking-results-card').style.display = 'none';
    navigateTo('claims-tracking');
});

// Main Log In / Log Out button routing
document.getElementById('link-login').addEventListener('click', (e) => {
    if (STATE.currentUser) {
        handleLogOut();
    } else {
        navigateTo('login');
    }
});

// Welcome Section Buttons
document.getElementById('btn-welcome-map').addEventListener('click', () => {
    // If user not logged in, redirect to login view
    if (!STATE.currentUser) {
        navigateTo('login');
        return;
    }
    // Already logged in as public (or admin will use admin UI)
    STATE.currentUser = 'public';
    navigateTo('map');
});


// Navigation Buttons - Switch between login and register views
document.getElementById('btn-go-register').addEventListener('click', () => {
    navigateTo('register');
});

document.getElementById('btn-go-login').addEventListener('click', () => {
    navigateTo('login');
});

// Footer Admin Login Button
document.getElementById('btn-footer-admin-login').addEventListener('click', () => {
    document.getElementById('modal-admin-login').classList.add('active');
});

// Close Admin Login Modal
document.getElementById('btn-modal-admin-close').addEventListener('click', () => {
    document.getElementById('modal-admin-login').classList.remove('active');
});

// Public Login Form
document.getElementById('form-public-login-action').addEventListener('submit', (e) => {
    e.preventDefault();
    STATE.currentUser = 'public';
    navigateTo('map');
});

// Public Register Form
document.getElementById('form-public-register-action').addEventListener('submit', (e) => {
    e.preventDefault();
    const name = document.getElementById('pub-reg-name').value.trim();
    const email = document.getElementById('pub-reg-email').value.trim();
    const pass = document.getElementById('pub-reg-pass').value.trim();
    
    if (!name || !email || !pass) {
        alert('Por favor complete todos los campos requeridos.');
        return;
    }
    
    // Simple validation
    if (pass.length < 4) {
        alert('La contraseÃ±a debe tener al menos 4 caracteres.');
        return;
    }
    
    alert('Â¡Registro exitoso! Bienvenido ' + name + '. Accediendo al sistema...');
    STATE.currentUser = 'public';
    navigateTo('map');
});

// Admin Login Form (Footer Modal)
document.getElementById('form-admin-login-footer').addEventListener('submit', (e) => {
    e.preventDefault();
    const email = document.getElementById('adm-login-email-footer').value.trim();
    const pass = document.getElementById('adm-login-pass-footer').value.trim();
    if (!email || !pass) {
        alert('Por favor ingrese correo y contraseÃ±a.');
        return;
    }
    // Hardâ€‘coded admin credentials
    if (email !== 'administrador@hotmail.com' || pass !== '123') {
        alert('Credenciales de administrador invÃ¡lidas.');
        return;
    }
    STATE.currentUser = 'admin';
    document.getElementById('modal-admin-login').classList.remove('active');
    navigateTo('admin');
});

// ----------------------------------------------------
// Public Map Sidebar Bindings
// ----------------------------------------------------
// Sidebar collapse toggle button
document.getElementById('sidebar-toggle').addEventListener('click', () => {
    const sidebar = document.getElementById('map-sidebar');
    sidebar.classList.toggle('collapsed');
    // Recalculate map container size after sidebar animation finished
    setTimeout(() => {
        if (STATE.mapInstance) STATE.mapInstance.invalidateSize();
    }, 400);
});

// Filter Change Triggers
const filterSelectors = ['filter-especie', 'filter-altura', 'filter-edad', 'filter-estado'];
filterSelectors.forEach(id => {
    document.getElementById(id).addEventListener('change', renderMapMarkers);
});

// Search trigger
document.getElementById('map-search-btn').addEventListener('click', searchAddress);
document.getElementById('map-search-input').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') searchAddress();
});

// Details Back Button
document.getElementById('btn-tree-back').addEventListener('click', () => {
    STATE.activeTree = null;
    switchSidebarPanel('filters');
});

// Action button to start claim form
document.getElementById('btn-make-claim').addEventListener('click', () => {
    // Check if user is logged in
    if (!STATE.currentUser) {
        alert('Debes iniciar sesiÃ³n para hacer un reclamo. SerÃ¡s redirigido al login.');
        navigateTo('login');
        return;
    }
    
    if (!STATE.activeTree) return;
    document.getElementById('claim-tree-id-label').textContent = `#${STATE.activeTree.id}`;
    document.getElementById('claim-tree-specie-label').textContent = STATE.activeTree.specie;
    switchSidebarPanel('claim-form');
});

// Claim Form Cancel / Back
document.getElementById('btn-claim-back-to-tree').addEventListener('click', () => {
    if (STATE.activeTree) showTreeDetails(STATE.activeTree);
});

// Claim Submit
document.getElementById('tree-claim-form').addEventListener('submit', submitClaimForm);

// Success panel close
document.getElementById('btn-claim-success-close').addEventListener('click', () => {
    switchSidebarPanel('filters');
});

// Copy code button
document.getElementById('btn-copy-claim-code').addEventListener('click', () => {
    const val = document.getElementById('claim-success-code').textContent;
    navigator.clipboard.writeText(val).then(() => {
        alert('CÃ³digo de seguimiento copiado al portapapeles.');
    });
});

// ----------------------------------------------------
// Claims Tracking Screen Search Bindings
// ----------------------------------------------------
document.getElementById('btn-search-tracking').addEventListener('click', searchClaimTracking);
document.getElementById('tracking-input-code').addEventListener('keypress', (e) => {
    if (e.key === 'Enter') searchClaimTracking();
});

// ----------------------------------------------------
// Admin Tabs & Management Bindings
// ----------------------------------------------------
document.getElementById('tab-btn-map').addEventListener('click', (e) => {
    // Set tab active
    document.querySelectorAll('.admin-tab-btn').forEach(btn => btn.classList.remove('active'));
    e.target.classList.add('active');
    // Show content
    document.querySelectorAll('.admin-tab-content').forEach(c => c.classList.remove('active'));
    document.getElementById('admin-tab-content-map').classList.add('active');
});

document.getElementById('tab-btn-table').addEventListener('click', (e) => {
    document.querySelectorAll('.admin-tab-btn').forEach(btn => btn.classList.remove('active'));
    e.target.classList.add('active');
    document.querySelectorAll('.admin-tab-content').forEach(c => c.classList.remove('active'));
    document.getElementById('admin-tab-content-table').classList.add('active');
    renderCensusExcelTable();
});

document.getElementById('tab-btn-claims').addEventListener('click', (e) => {
    document.querySelectorAll('.admin-tab-btn').forEach(btn => btn.classList.remove('active'));
    // Make sure we trigger on button element, not internal child
    const btn = e.target.closest('button');
    btn.classList.add('active');
    document.querySelectorAll('.admin-tab-content').forEach(c => c.classList.remove('active'));
    document.getElementById('admin-tab-content-claims').classList.add('active');
    renderClaimsInboxTable();
});

// Admin redirect back to map tab handler
document.getElementById('btn-admin-go-map').addEventListener('click', () => {
    // Toggle user to public map to inspect map interface with loaded changes
    navigateTo('map');
});

// Spreadsheet filter change handlers
document.getElementById('excel-search-input').addEventListener('input', renderCensusExcelTable);
document.getElementById('excel-filter-specie').addEventListener('change', renderCensusExcelTable);
document.getElementById('excel-filter-estado').addEventListener('change', renderCensusExcelTable);

// Export to CSV
document.getElementById('btn-admin-export-csv').addEventListener('click', exportToCSV);

// Add Tree Modal opener
document.getElementById('btn-admin-add-tree').addEventListener('click', () => openTreeModal(null));

// Submit Forms
document.getElementById('admin-tree-form').addEventListener('submit', submitTreeForm);
document.getElementById('admin-claim-update-form').addEventListener('submit', submitClaimUpdateForm);

// Table Header Sorting Event Binding
document.querySelectorAll('#census-excel-table th[data-sort]').forEach(th => {
    th.addEventListener('click', () => {
        const key = th.getAttribute('data-sort');
        if (excelSortConfig.key === key) {
            excelSortConfig.direction = excelSortConfig.direction === 'asc' ? 'desc' : 'asc';
        } else {
            excelSortConfig.key = key;
            excelSortConfig.direction = 'asc';
        }
        renderCensusExcelTable();
    });
});

// ----------------------------------------------------
// Modal Close Overlay Button Bindings
// ----------------------------------------------------
// Contact modal
document.getElementById('btn-modal-contacto-close').addEventListener('click', () => {
    document.getElementById('modal-contacto').classList.remove('active');
});
document.getElementById('btn-modal-contacto-close-ok').addEventListener('click', () => {
    document.getElementById('modal-contacto').classList.remove('active');
});

// Tree form modal
document.getElementById('btn-modal-tree-close').addEventListener('click', () => {
    document.getElementById('modal-tree-form').classList.remove('active');
});

// Claim update modal
document.getElementById('btn-modal-claim-close').addEventListener('click', () => {
    document.getElementById('modal-claim-manage').classList.remove('active');
});

// Click outside modal overlays to close them
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            overlay.classList.remove('active');
        }
    });
});

// Initialize routing to Welcome Screen
navigateTo('welcome');
