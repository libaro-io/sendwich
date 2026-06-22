import mitt, { type Emitter } from 'mitt';

export type AppEvents = {
    updateOrders: void;
    updateSelectedRunner: void;
};

// Single shared mitt instance. Imported here and registered on
// globalProperties in app.js so Options-API (`this.emitter`) and
// `<script setup>` (import) talk to the same bus.
export const emitter: Emitter<AppEvents> = mitt<AppEvents>();