import $ from 'jquery';

// Required due to Bootstrap/Vite/jQuery issue
// https://github.com/twbs/bootstrap/issues/38914#issuecomment-2108123487
// https://stackoverflow.com/a/75920162
window.$ = window.jQuery = $;
