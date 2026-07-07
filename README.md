# sistema-de-gestion-de-arboles
Aplicación web que esta orientada a la gestión de arboles y posterior visualización geográfica de los mismos.

# App de Mapeado y Gestión Integral de Arbolado Urbano

## Contexto de la aplicación
A través de la observación directa del sector de gestión urbanística y el mantenimiento del espacio público, el equipo ha identificado una problemática significativa en la metodología de trabajo actual respecto al arbolado. La información de los distintos ejemplares es manejada de manera fragmentada por los inspectores, distribuyéndose en múltiples hojas de cálculo, lo cual ralentiza y dificulta la búsqueda de datos. Simultáneamente, la plataforma pública donde los vecinos gestionan denuncias por veredas rotas, podas o extracciones resulta confusa, generando incertidumbre en el ciudadano sobre el estado y seguimiento de sus solicitudes.

## Objetivos y Uso del Sistema
La plataforma tiene como eje central un mapa interactivo que visualizará los árboles censados y geolocalizados, vinculados directamente a una base de datos centralizada. El sistema busca optimizar la gestión operativa y la comunicación estableciendo dos perfiles de usuario:

### Perfiles de Usuario

*   **Inspector:** Podrá visualizar la información detallada de cada árbol mediante una tarjeta de identificación (carnet), con la opción de expandir los datos técnicos o acceder a registros históricos. Tendrá la capacidad de gestionar reclamos pendientes y actualizar el estado vital y de mantenimiento de los ejemplares (última poda, extracción, fallecimiento, etc.). Además, será la autoridad encargada de validar y aprobar los nuevos censos o mapeos propuestos en el sistema.
*   **Usuario Común:** Tendrá acceso para visualizar el estado del arbolado público y proponer mapeos de manera no oficial. Asimismo, podrá seleccionar un ejemplar específico en el mapa para emitir un reclamo formal mediante un formulario integrado, el cual será derivado al inspector correspondiente.

> *De este modo, se fomenta la participación ciudadana en el relevamiento de nuevos árboles, manteniendo el control de calidad y la oficialidad de los datos exclusivamente en manos de personal capacitado.*

## Desafíos y Limitaciones del Proyecto
*   **Identificación y unificación de datos:** El principal desafío técnico es estructurar la información preexistente. Históricamente, los árboles carecían de un número identificatorio único (ID). Si bien el Censo 2025/2026 introdujo esta medida, los registros visuales anteriores (como el Censo 2018) no cuentan con IDs relacionales directos.
*   **Delimitación del alcance:** El volumen de funcionalidades (módulo de inspectores, módulo ciudadano y sistema colaborativo de mapeo) representa un desafío respecto al scope inicial del desarrollo.
*   **Sistemas concurrentes:** El gobierno local posee actualmente una aplicación de uso interno para el censo en curso (a la cual el equipo tiene acceso para fines de análisis). El reto será plantear una herramienta superadora que integre el flujo de reclamos.
*   **Viabilidad y adopción:** Al tratarse mayoritariamente de arbolado en espacio público, la implementación a gran escala dependería de la adopción por parte de gobiernos municipales o provinciales, siendo los espacios verdes privados un nicho menor.
*   **Estandarización de procesos:** El relevamiento inicial de los procesos operativos de inspección se basó en protocolos estandarizados de Espacio Público. El desafío es lograr una arquitectura de software lo suficientemente estandarizada para adaptarse a los protocolos de cualquier otra comuna, distrito o entidad privada.

## Beneficios y Propuesta de Valor
*   **Innovación local:** Actualmente no existe un software o aplicación unificada de uso masivo en Argentina que aborde esta gestión de manera integral.
*   **Alineación a futuro:** La plataforma se adelanta a la necesidad inminente de los gobiernos locales de unificar los informes de arbolado bajo un sistema basado en IDs.
*   **Optimización laboral:** Facilita y agiliza el trabajo administrativo y de campo para el personal encargado del mantenimiento, tanto en el sector público como en el privado.
*   **Validación de mercado:** Este tipo de soluciones de software ya son utilizadas con éxito en grandes metrópolis como Nueva York o Hong Kong.
*   **Concientización ambiental:** Unificar y hacer accesible esta información no solo optimiza la gestión administrativa, sino que visibiliza y pone en valor el impacto de los espacios verdes para el ciudadano común.

## 🔗 Referencias
*   Mapa del censo 2018 - CABA.
*   Mapa interactivo de árboles de Nueva York.
*   Aplicación de gestión privada en México (referencia visual e iniciativa).
