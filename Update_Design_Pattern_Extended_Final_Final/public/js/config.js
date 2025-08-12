/**
 * Global configuration for the client-side application.
 */
export const AppConfig = {
    // Server update rate in milliseconds. 
    // This MUST MATCH the server's FIXED_TIMESTEP.
    // 1 Hz = 1000ms, 5 Hz = 200ms, 10 Hz = 100ms
    // UPDATE_INTERVAL_MS: 100, // Using 10Hz as an example
    UPDATE_INTERVAL_MS:100,

    // The delay in milliseconds to render behind the latest server state.
    // This allows for smooth interpolation. A good default is the update interval.
    // INTERPOLATION_DELAY_MS: 100,
    INTERPOLATION_DELAY_MS: 100,
};