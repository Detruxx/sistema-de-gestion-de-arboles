/**
 * Interfaz (Mapa Publico): Logica de manipulacion de paneles flotantes y controles visuales dentro del mapa.
 * En movil, el sidebar se comporta como un bottom-sheet con 3 estados y soporte de swipe.
 */

/* Umbral minimo en pixeles que el dedo debe recorrer para que se considere un swipe valido */
const SWIPE_THRESHOLD = 50;

/* Los 3 estados posibles del bottom-sheet en movil */
const SHEET_STATES = ['sidebar-closed', 'sidebar-peek', 'sidebar-open'];

/**
 * Detecta si el dispositivo actual es movil basandose en el ancho de la ventana.
 * Coincide con el breakpoint de 768px usado en las media queries CSS.
 */
function IsMobile() {
    return window.innerWidth <= 768;
}

/**
 * Obtiene el estado actual del sidebar entre los 3 posibles.
 * Devuelve el nombre de la clase CSS activa ('sidebar-closed', 'sidebar-peek', 'sidebar-open').
 */
function GetCurrentState(sidebar) {
    for (const state of SHEET_STATES) {
        if (sidebar.classList.contains(state)) {
            return state;
        }
    }
    return 'sidebar-closed';
}

/**
 * Cambia el estado del bottom-sheet removiendo todos los estados previos
 * y aplicando el nuevo. Esto permite transiciones limpias via CSS.
 */
function SetSheetState(sidebar, new_state) {
    SHEET_STATES.forEach(state => sidebar.classList.remove(state));
    sidebar.classList.add(new_state);
}

/**
 * Calcula el siguiente estado del bottom-sheet basandose en la direccion
 * y distancia del swipe del usuario.
 * - Swipe hacia arriba: avanza al siguiente estado mas abierto.
 * - Swipe hacia abajo: retrocede al siguiente estado mas cerrado.
 */
function GetNextState(current_state, delta_y) {
    const current_index = SHEET_STATES.indexOf(current_state);
    const is_swipe_up = delta_y < -SWIPE_THRESHOLD;
    const is_swipe_down = delta_y > SWIPE_THRESHOLD;

    if (is_swipe_up && current_index < SHEET_STATES.length - 1) {
        return SHEET_STATES[current_index + 1];
    }
    if (is_swipe_down && current_index > 0) {
        return SHEET_STATES[current_index - 1];
    }

    return current_state;
}

/**
 * Configura los eventos tactiles (touch) sobre el handle del bottom-sheet
 * para permitir arrastrar y soltar (drag & release) con feedback visual en tiempo real.
 */
function SetupTouchSwipe(sidebar, handle_element) {
    let touch_start_y = 0;
    let sheet_start_translate_y = 0;
    let is_touching = false;

    /* Obtiene el translateY actual del sidebar desde su estilo computado */
    function GetCurrentTranslateY() {
        const computed_style = window.getComputedStyle(sidebar);
        const matrix = new DOMMatrix(computed_style.transform);
        return matrix.m42;
    }

    /* Inicio del toque: registra la posicion inicial */
    handle_element.addEventListener('touchstart', function(touch_event) {
        if (!IsMobile()) return;

        is_touching = true;
        touch_start_y = touch_event.touches[0].clientY;
        sheet_start_translate_y = GetCurrentTranslateY();

        sidebar.classList.add('is-dragging');
    }, { passive: true });

    /* Movimiento del dedo: actualiza la posicion del sheet en tiempo real */
    handle_element.addEventListener('touchmove', function(touch_event) {
        if (!is_touching || !IsMobile()) return;

        const current_touch_y = touch_event.touches[0].clientY;
        const drag_delta = current_touch_y - touch_start_y;

        /* Solo permitir arrastrar hacia abajo si ya esta en el tope,
           y hacia arriba si no esta completamente abierto */
        const new_translate_y = Math.max(0, sheet_start_translate_y + drag_delta);
        sidebar.style.transform = `translateY(${new_translate_y}px)`;
    }, { passive: true });

    /* Fin del toque: determina el estado final basandose en la distancia recorrida */
    handle_element.addEventListener('touchend', function(touch_event) {
        if (!is_touching || !IsMobile()) return;

        is_touching = false;
        sidebar.classList.remove('is-dragging');
        sidebar.style.transform = '';

        const touch_end_y = touch_event.changedTouches[0].clientY;
        const total_delta = touch_end_y - touch_start_y;

        const current_state = GetCurrentState(sidebar);
        const next_state = GetNextState(current_state, total_delta);
        SetSheetState(sidebar, next_state);
    }, { passive: true });
}

/**
 * Configura todos los controles de la interfaz del mapa:
 * - Toggle del sidebar (click en desktop, swipe en movil)
 * - Boton de volver
 * - Menu desplegable de filtros
 */
export function setupUI() {
    const sidebar = document.getElementById('tree-sidebar');
    const toggle_btn = document.getElementById('toggle-sidebar');
    const btn_tree_back = document.getElementById('btn-tree-back');
    const btn_toggle_filters = document.getElementById('btn-toggle-filters');
    const filter_dropdown_menu = document.getElementById('filter-dropdown-menu');

    /* Toggle del panel: en desktop alterna abierto/cerrado, en movil cicla estados */
    if (toggle_btn) {
        toggle_btn.addEventListener('click', () => {
            if (IsMobile()) {
                const current_state = GetCurrentState(sidebar);
                if (current_state === 'sidebar-closed') {
                    SetSheetState(sidebar, 'sidebar-peek');
                } else if (current_state === 'sidebar-peek') {
                    SetSheetState(sidebar, 'sidebar-open');
                } else {
                    SetSheetState(sidebar, 'sidebar-closed');
                }
            } else {
                sidebar.classList.toggle('sidebar-closed');
            }
        });

        /* Configurar swipe tactil en movil */
        if (sidebar) {
            SetupTouchSwipe(sidebar, toggle_btn);
        }
    }

    /* Boton "Volver": cierra el panel */
    if (btn_tree_back) {
        btn_tree_back.addEventListener('click', () => {
            if (IsMobile()) {
                SetSheetState(sidebar, 'sidebar-closed');
            } else {
                sidebar.classList.add('sidebar-closed');
            }
        });
    }

    /* Alternar el menu desplegable de filtros */
    if (btn_toggle_filters && filter_dropdown_menu) {
        btn_toggle_filters.addEventListener('click', (click_event) => {
            click_event.stopPropagation();
            btn_toggle_filters.classList.toggle('active');
            filter_dropdown_menu.classList.toggle('active');
        });

        /* Cerrar el menu si se hace clic fuera del mismo */
        document.addEventListener('click', (click_event) => {
            if (!filter_dropdown_menu.contains(click_event.target) && click_event.target !== btn_toggle_filters && !btn_toggle_filters.contains(click_event.target)) {
                btn_toggle_filters.classList.remove('active');
                filter_dropdown_menu.classList.remove('active');
            }
        });
    }
}

/**
 * Abre el sidebar mostrando los detalles del arbol seleccionado.
 * En movil lo lleva al estado "peek" para mostrar la informacion basica.
 * En desktop simplemente remueve la clase de cerrado.
 */
export function openSidebar() {
    const sidebar = document.getElementById('tree-sidebar');
    if (sidebar) {
        if (IsMobile()) {
            SetSheetState(sidebar, 'sidebar-peek');
        } else {
            sidebar.classList.remove('sidebar-closed');
        }
    }
}
