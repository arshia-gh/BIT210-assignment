import ApiError from './ApiError';
import {
	RESOURCE,
	AUTH_RESOURCE,
	METHOD,
	ERROR_CODE,
	isValidResource
} from './utils';
import storage from './extendedLocalStorage';
import Administrator from '../model/Administrator';
import Patient from '../model/Patient';

/**
 * @typedef {Object} options - request options
 * @property {METHOD?} options.method - request method (ex: METHOD.GET)
 * @property {Object.<string, string>?} options.query - query string (ex: /test?id=10)
 * @property {Object?} options.content - data for post and patch requests
 */

/**
 * @param {RESOURCE} resource - target resource uri (ex: /test)
 * @param {options} options - request options
 *
 * @return {Promise} an object or array containing the retrieved data
 */
export const request = async (
	resource,
	options = {
		method: METHOD.GET,
		content: {},
		query: {}
	}
) => {
	if (options == null) options = {};
	if (options.query == null) options.query = {};
	if (options.content == null) options.content = {};
	if (options.method == null) options.method = METHOD.GET;

	if (!isValidResource(resource.URI)) {
		throw new ApiError('Invalid resource', ERROR_CODE.NOT_FOUND);
	}

	let results;
	switch (options.method) {
		case METHOD.POST:
			results = storage.create(resource.URI, options.content);
			break;
		case METHOD.GET:
			results = storage.read(resource.URI, options.query);
			break;
		case METHOD.PATCH:
			results = storage.update(
				resource.URI,
				options.content,
				options.query
			);
			break;
		default:
			throw new ApiError('Invalid method', BAD_REQUEST);
	}
	if (Array.isArray(results)) {
		return results.map(resource.MODEL.fromParsedJson);
	}
	return results;
};

/**
 *
 * @param {AUTH_RESOURCE} resource
 * @param {Object} content
 * @param {string} content.username
 * @param {string} content.password
 */
export const auth = async (resource, content = {}) => {
	if (content == null) content = {};
	if (content.username == null) content.username = null;
	if (content.password == null) content.password = null;
	if (resource == null) {
		throw new ApiError('Invalid resource', ERROR_CODE.NOT_FOUND);
	}
	switch (resource) {
		case AUTH_RESOURCE.AUTHENTICATE:
			return storage.authenticate();
		case AUTH_RESOURCE.LOGIN:
			return storage.login(content.username, content.password);
		case AUTH_RESOURCE.LOGOUT:
			return storage.logout();
	}
};
