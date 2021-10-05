import APIRequest from './request';
import { METHOD, RESOURCE } from './utils';
import { ProjectConfig } from 'javascript/config';
import storage from './extendedLocalStorage';

if (storage.isEmpty() || ProjectConfig.mode === 'development') {
	storage.seed();
}

export default { request: APIRequest, METHOD, RESOURCE };
export { APIRequest as request, METHOD, RESOURCE };
