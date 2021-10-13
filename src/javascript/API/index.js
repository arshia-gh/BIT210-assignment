import { request, auth } from './Api';
import { METHOD, RESOURCE, AUTH_RESOURCE, ERROR_CODE } from './utils';
import storage from './extendedLocalStorage';
import seed from './seed';

export default {
	request,
	auth,
	seed,
	storage,
	METHOD,
	RESOURCE,
	AUTH_RESOURCE,
	ERROR_CODE
};
export {
	request,
	auth,
	seed,
	storage,
	METHOD,
	RESOURCE,
	AUTH_RESOURCE,
	ERROR_CODE
};
