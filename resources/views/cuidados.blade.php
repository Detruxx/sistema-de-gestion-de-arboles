@extends('layouts.app')

@section('title', 'Cuidados | TreeBA')
@section('navbar-class', 'scrolled')
@section('active-cuidados', 'active')

@section('content')
    <main class="cuidados-page-container">
        <section class="cuidados-header reveal">
            <h1 class="hero-title">Cuidado del Arbolado</h1>
            <p class="section-subtitle">
                Guía de buenas prácticas y normativas esenciales para proteger y conservar el bosque urbano de nuestra ciudad.
            </p>
        </section>

        <section class="tips-grid">
            <div class="tip-card reveal delay-1" 
                  data-badge="Cuidado Diario" 
                  data-title="Riego Consciente y Eficiente" 
                  data-description="El agua es vital para el establecimiento de los árboles en las veredas, especialmente durante los primeros 3 años posteriores a su plantación. Durante este periodo, sus raíces aún no están lo suficientemente extendidas para buscar agua por sí mismas de manera eficiente en el suelo urbano." 
                  data-tips="Riega con 20 a 40 litros de agua una vez a la semana, preferentemente al caer la tarde.;Hazlo de forma lenta para que el suelo absorba toda la humedad y no se escurra.;En verano o épocas de sequía prolongada, duplica la frecuencia a dos veces por semana.;Evita mojar las hojas a pleno sol para prevenir quemaduras de sol y proliferación de hongos." 
                  data-image="https://images.unsplash.com/photo-1585320806297-9794b3e4eeae?w=600&auto=format&fit=crop">
                <div class="tip-card-header">
                    <span class="tip-icon-box">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path></svg>
                    </span>
                    <h3>Riego Consciente</h3>
                </div>
                <p>
                    Los árboles jóvenes necesitan riego regular (20 a 40 litros semanales) durante sus primeros 3 años de vida. Riega lentamente al atardecer para evitar la evaporación inmediata.
                </p>
                <div class="tip-card-footer">
                    <div class="tip-badge">Cuidado Diario</div>
                    <span class="read-more-link">
                        Leer más
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </span>
                </div>
            </div>

            <div class="tip-card reveal delay-2" 
                  data-badge="Normativa Legal" 
                  data-title="Poda y Mantenimiento del Arbolado" 
                  data-description="La Ley N° 3263 de la Ciudad de Buenos Aires prohíbe terminantemente la poda, tala o cualquier daño al arbolado público por parte de particulares. La poda inadecuada debilita la estructura del árbol y reduce drásticamente su expectativa de vida al permitir la entrada de plagas." 
                  data-tips="Solicita el servicio de poda a través de la línea de reclamos del Gobierno de la Ciudad (BA 147).;La temporada oficial de poda se realiza exclusivamente en invierno (mayo a agosto) para evitar la pérdida excesiva de savia.;La poda particular sin autorización conlleva multas severas y sanciones legales por daño al patrimonio público.;Solo los inspectores comunales capacitados pueden determinar si un árbol requiere poda de despeje o saneamiento." 
                  data-image="https://images.unsplash.com/photo-1592150621744-aca64f48394a?w=600&auto=format&fit=crop">
                <div class="tip-card-header">
                    <span class="tip-icon-box">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"></circle><circle cx="6" cy="18" r="3"></circle><line x1="9.8" y1="8.2" x2="21" y2="19"></line><line x1="9.8" y1="15.8" x2="21" y2="5"></line></svg>
                    </span>
                    <h3>Poda Reglamentada</h3>
                </div>
                <p>
                    La poda particular en la vía pública está prohibida por la Ley 3263. Las tareas autorizadas por la comuna se realizan exclusivamente en invierno (mayo a agosto). ¡No podes por tu cuenta!
                </p>
                <div class="tip-card-footer">
                    <div class="tip-badge warning">Normativa Legal</div>
                    <span class="read-more-link">
                        Leer más
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </span>
                </div>
            </div>

            <div class="tip-card reveal delay-3" 
                  data-badge="Requiere Permiso" 
                  data-title="Plantación Autorizada de Especies" 
                  data-description="Para plantar un árbol en la vereda pública se requiere obligatoriamente una autorización de la comuna correspondiente. Cada calle y ancho de vereda cuenta con un catálogo de especies aptas para evitar daños en la infraestructura subterránea y aérea." 
                  data-tips="Realiza la solicitud formal a través de la <a href='https://buenosaires.gob.ar/tramites/plantacion-de-arbol' target='_blank' class='modal-inline-link'>web oficial del gobierno de la ciudad (GCBA)</a>.;El vivero de la ciudad proveerá e indicará la especie adecuada (por ejemplo, Jacarandá, Crespón o Árbol de Judea).;Nunca plantes especies invasoras, con espinas, o raíces sumamente agresivas (como gomeros o sauces) en la acera pública.;Asegúrate de dejar el espacio de paso reglamentario para peatones y personas con movilidad reducida." 
                  data-image="https://images.unsplash.com/photo-1599599810769-bcde5a160d32?w=600&auto=format&fit=crop">
                <div class="tip-card-header">
                    <span class="tip-icon-box">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 19v3M12 3L9 8h1.5L7.5 13h1.5L5 19h14l-4-6h1.5l-3-5h1.5Z"/></svg>
                    </span>
                    <h3>Plantación Autorizada</h3>
                </div>
                <p>
                    Para plantar en la vereda debes solicitar autorización. El gobierno define la especie adecuada para tu calle, evitando futuros daños en cañerías, cableados o la propia acera.
                </p>
                <div class="tip-card-footer">
                    <div class="tip-badge warning">Requiere Permiso</div>
                    <span class="read-more-link">
                        Leer más
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </span>
                </div>
            </div>

            <div class="tip-card reveal delay-1" 
                  data-badge="Protección" 
                  data-title="Plantera Libre de Cemento y Químicos" 
                  data-description="La plantera (el espacio cuadrado de tierra en la vereda) es la boca de alimentación y respiración principal del árbol. El cementado o pavimentación de la plantera asfixia las raíces, impidiendo el paso del oxígeno y el agua de lluvia." 
                  data-tips="Mantén la tierra de la plantera suelta, desmalezada y completamente libre de escombros o basura.;Queda estrictamente prohibido rellenar la plantera con cemento, baldosas o colocar rejas totalmente cerradas.;Nunca arrojes agua con detergentes, lavandina, aceites ni desinfectantes químicos en la plantera.;Planta flores de raíz superficial en la plantera; ayudan a retener la humedad del suelo y embellecen la vereda." 
                  data-image="https://images.unsplash.com/photo-1502082553048-f009c37129b9?w=600&auto=format&fit=crop">
                <div class="tip-card-header">
                    <span class="tip-icon-box">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                    </span>
                    <h3>Plantera Libre</h3>
                </div>
                <p>
                    Mantén el espacio de tierra libre de cemento, piedras y basura. Nunca viertas agua con detergentes, lavandina ni productos químicos, ya que envenenan las raíces del árbol.
                </p>
                <div class="tip-card-footer">
                    <div class="tip-badge">Protección</div>
                    <span class="read-more-link">
                        Leer más
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </span>
                </div>
            </div>

            <div class="tip-card reveal delay-2" 
                  data-badge="Recomendación" 
                  data-title="Acolchado y Nutrición Orgánica (Mulch)" 
                  data-description="El mulching consiste en colocar una capa de material orgánico triturado en la base del árbol. Este proceso emula el suelo de un bosque natural, previniendo la compactación urbana y mejorando notablemente la salud radicular." 
                  data-tips="Coloca una capa de 5 a 10 cm de chips de madera triturados, corteza de pino u hojas secas en la base.;Deja un espacio libre de unos 5 cm alrededor del tronco para evitar la humedad excesiva y pudrición de la corteza.;El mulch retiene hasta un 50% más de humedad en el suelo, reduciendo la necesidad de riegos constantes.;Al descomponerse lentamente, aporta nutrientes esenciales y materia orgánica al suelo de la cazuela." 
                  data-image="https://images.unsplash.com/photo-1599599810694-b5b37f88da22?w=600&auto=format&fit=crop">
                <div class="tip-card-header">
                    <span class="tip-icon-box">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 3.5 1 9.8a7 7 0 0 1-9 8.2z"></path><path d="M9 22v-4"></path></svg>
                    </span>
                    <h3>Mulch o Acolchado</h3>
                </div>
                <p>
                    Colocar chips de madera u hojas secas en la base del tronco retiene la humedad, regula la temperatura del suelo y evita el crecimiento de malezas que compiten por nutrientes.
                </p>
                <div class="tip-card-footer">
                    <div class="tip-badge">Recomendación</div>
                    <span class="read-more-link">
                        Leer más
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </span>
                </div>
            </div>

            <div class="tip-card reveal delay-3" 
                  data-badge="Prevención" 
                  data-title="Protección de la Corteza y el Tronco" 
                  data-description="La corteza es la 'piel' protectora del árbol. Cualquier herida física expuesta en el tronco interrumpe la circulación de savia e invita de forma directa a hongos, insectos y plagas fatales a pudrir la madera interna." 
                  data-tips="Evita atar bicicletas, motos o colocar carteles publicitarios con clavos o cadenas alrededor del tronco.;No utilices la corteza para fijar cables, luces navideñas tensas o colgar elementos punzantes.;Ten extremo cuidado al usar bordadoras de césped o cortadoras cerca de la base para no 'anillar' y matar al árbol.;Si observas heridas con savia expuesta o presencia de hongos lignívoros, repórtalo de inmediato para tratamiento." 
                  data-image="https://images.unsplash.com/photo-1542273917363-3b1817f69a2d?w=600&auto=format&fit=crop">
                <div class="tip-card-header">
                    <span class="tip-icon-box">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    </span>
                    <h3>Proteger la Corteza</h3>
                </div>
                <p>
                    La corteza es la "piel" del árbol. Evita clavar carteles, atar bicicletas con cadenas pesadas o dañarla con bordadoras de césped; las heridas abren paso a hongos y plagas fatales.
                </p>
                <div class="tip-card-footer">
                    <div class="tip-badge">Prevención</div>
                    <span class="read-more-link">
                        Leer más
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </span>
                </div>
            </div>
        </section>
    </main>

    <!-- Modal de Detalles de Cuidado -->
    <div id="care-modal" class="care-modal-overlay">
        <!-- Botones de navegación del modal -->
        <button class="modal-nav-btn prev" id="modal-prev-btn" aria-label="Anterior">&lsaquo;</button>
        <button class="modal-nav-btn next" id="modal-next-btn" aria-label="Siguiente">&rsaquo;</button>

        <div class="care-modal-container">
            <button class="care-modal-close" id="modal-close-btn">&times;</button>
            <div class="care-modal-layout">
                <div class="care-modal-content">
                    <span class="care-modal-badge" id="modal-badge">CATEGORÍA</span>
                    <h2 class="care-modal-title" id="modal-title">Título del Cuidado</h2>
                    <p class="care-modal-body" id="modal-body">
                        Aquí va el texto detallado...
                    </p>
                    <div class="care-modal-action-box">
                        <h4>Buenas Prácticas:</h4>
                        <ul class="care-modal-tips-list" id="modal-tips-list">
                            <!-- Lista de tips -->
                        </ul>
                    </div>
                </div>
                <div class="care-modal-image-panel">
                    <img id="modal-image" src="" alt="Cuidado del árbol">
            </div>
        </div>
    </div>
@endsection

@section('footer')

@endsection

@section('scripts')
    
    <script src="{{ asset('js/modal.js') }}"></script>
@endsection
