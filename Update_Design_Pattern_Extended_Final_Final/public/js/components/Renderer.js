// /**
//  * Manages rendering entities to the DOM, including extrapolation
//  * and efficient DOM updates.
//  */
// export class Renderer {
//     /**
//      * @param {HTMLElement} worldElement The container for the game world.
//      */
//     constructor(worldElement) {
//         this.worldElement = worldElement;
//         this.worldWidth = worldElement.clientWidth;
//         this.worldHeight = worldElement.clientHeight;
        
//         // This map is the key to performance.
//         // We store entity divs by their ID to avoid recreating them.
//         this.entityDivs = new Map();
//     }

//     /**
//      * Clears all entities from the world.
//      */
//     clear() {
//         this.worldElement.innerHTML = '';
//         this.entityDivs.clear();
//     }

//     /**
//      * Renders the game state.
//      * @param {Object} serverState The latest state from the server.
//      * @param {number} timeSinceUpdate The time in seconds since the server state was generated.
//      */
//     render(serverState, timeSinceUpdate) {
//         if (!serverState || !serverState.entities) return;

//         const seenEntityIds = new Set();

//         // Update existing entities and create new ones
//         for (const entity of serverState.entities) {
//             seenEntityIds.add(entity.id);

//             const { renderX, renderY } = this._calculateRenderPosition(entity, timeSinceUpdate);

//             let div = this.entityDivs.get(entity.id);
//             if (!div) {
//                 div = this._createEntityDiv(entity);
//                 this.entityDivs.set(entity.id, div);
//                 this.worldElement.appendChild(div);
//             }

//             // Update its position using the more performant `transform`.
//             const finalX = (renderX / 100) * this.worldWidth;
//             const finalY = (renderY / 100) * this.worldHeight;
//             div.style.transform = `translate(${finalX}px, ${finalY}px)`;
//         }

//         // Remove divs for entities that no longer exist
//         for (const [id, div] of this.entityDivs.entries()) {
//             if (!seenEntityIds.has(id)) {
//                 div.remove();
//                 this.entityDivs.delete(id);
//             }
//         }
//     }

//     /**
//      * Creates a new DOM element for an entity.
//      * @private
//      */
//     _createEntityDiv(entity) {
//         const div = document.createElement('div');
//         div.className = `entity ${entity.type.toLowerCase()}`;
//         return div;
//     }

//     /**
//      * Calculates the extrapolated position for an entity.
//      * @private
//      */
//     _calculateRenderPosition(entity, timeSinceUpdate) {
//         let renderX = entity.x;
//         let renderY = entity.y;

//         // Apply extrapolation only to predictable moving entities
//         if (entity.type === 'LightningBolt' || entity.type === 'Skeleton') {
//             renderX += entity.vx * timeSinceUpdate;
//             renderY += entity.vy * timeSinceUpdate;

//             if (entity.type === 'Skeleton') {
//                 if (renderX > 100) {
//                     renderX = 100 - (renderX - 100);
//                 } else if (renderX < 0) {
//                     renderX = -renderX;
//                 }
//             }
//         }
        
//         // Return a clamped, safe position
//         return {
//             renderX: Math.max(0, Math.min(100, renderX)),
//             renderY: Math.max(0, Math.min(100, renderY))
//         };
//     }
// }


/**
 * Manages rendering entities to the DOM, including interpolation
 * and efficient DOM updates.
 */
export class Renderer {
    /**
     * @param {HTMLElement} worldElement The container for the game world.
     */
    constructor(worldElement) {
        this.worldElement = worldElement;
        this.worldWidth = worldElement.clientWidth;
        this.worldHeight = worldElement.clientHeight;

        // This map is the key to performance.
        // We store entity divs by their ID to avoid recreating them.
        this.entityDivs = new Map();
    }

    clear() {
        this.worldElement.innerHTML = '';
        this.entityDivs.clear();
    }

    /**
     * Renders the game state by interpolating between two server states.
     * @param {Object} prevState The older server state.
     * @param {Object} targetState The newer server state.
     * @param {number} interpolationFactor The alpha value (0.0 to 1.0) for interpolation.
     */
    render(prevState, targetState, interpolationFactor) {
        if (!targetState || !targetState.entities) return;

        const seenEntityIds = new Set();
        const prevEntityMap = new Map(prevState.entities.map(e => [e.id, e]));

        // Update existing entities and create new ones based on the target state
        for (const targetEntity of targetState.entities) {
            seenEntityIds.add(targetEntity.id);
            const prevEntity = prevEntityMap.get(targetEntity.id);

            let renderX, renderY;

            if (prevEntity) {
                // This entity exists in both states, so we interpolate its position.
                ({ renderX, renderY } = this._calculateInterpolatedPosition(prevEntity, targetEntity, interpolationFactor));
            } else {
                // This entity is new (only in targetState), render it at its final position.
                renderX = targetEntity.x;
                renderY = targetEntity.y;
            }

            let div = this.entityDivs.get(targetEntity.id);
            if (!div) {
                div = this._createEntityDiv(targetEntity);
                this.entityDivs.set(targetEntity.id, div);
                this.worldElement.appendChild(div);
            }

            // Update its position using the more performant `transform`.
            const finalX = (renderX / 100) * this.worldWidth;
            const finalY = (renderY / 100) * this.worldHeight;
            div.style.transform = `translate(${finalX}px, ${finalY}px)`;
        }

        // Remove divs for entities that no longer exist in the target state
        for (const [id, div] of this.entityDivs.entries()) {
            if (!seenEntityIds.has(id)) {
                div.remove();
                this.entityDivs.delete(id);
            }
        }
    }

    /**
     * Creates a new DOM element for an entity.
     * @private
     */
    _createEntityDiv(entity) {
        const div = document.createElement('div');
        div.className = `entity ${entity.type.toLowerCase()}`;
        return div;
    }

    /**
     * Calculates the interpolated position for an entity.
     * @private
     */
    _calculateInterpolatedPosition(prevEntity, targetEntity, factor) {
        // Linear interpolation (lerp) for smooth movement
        const renderX = prevEntity.x + (targetEntity.x - prevEntity.x) * factor;
        const renderY = prevEntity.y + (targetEntity.y - prevEntity.y) * factor;

        return { renderX, renderY };
    }
}