// dev
export { default as resetLocalStorage } from './resetLocalStorage';
export { default as seedLocalStorage } from './seedLocalStorage';
// endDev

import API from './api';
import { METHOD, RESOURCE } from './util';

export { API as request, METHOD, RESOURCE };
export default { request: API, METHOD, RESOURCE };
