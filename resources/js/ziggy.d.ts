import type { Router, RouteUrl } from 'ziggy-js';

declare global {
    function route(): Router;
    function route(name: string): RouteUrl;
    function route(
        name: string,
        params?: Record<string, unknown> | string | number,
        absolute?: boolean,
    ): RouteUrl;
}

export {};