("use strict");
function queryParams(p) {
    return {
        limit: p.limit,
        offset: p.offset,   // send offset directly, not page
        sort: p.sort,
        order: p.order,
        search: p.search,   // search term
    };
}


window.icons = {
    refresh: "bx-refresh",
    toggleOn: "bx-toggle-right",
    toggleOff: "bx-toggle-left",
    fullscreen: "bx-fullscreen",
    columns: "bx-list-ul",
    export_data: "bx-list-ul",
};
