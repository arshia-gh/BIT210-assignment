import { METHOD, getResourceURL } from './utils';

/* [TODO]
	[*] Create a localStorage wrapper and provide easy localdb swapping.
	[-] Provide a global interface for defining custom request types
	[-] Modularize error checking
	[-] Provide API Error classes for smoother error handling
*/

import storage from './extendedLocalStorage';

const APIGetRequest = async (resourceURL, parameters) => {
	const resourceList = storage.get(resourceURL);

	if (parameters == null || parameters.length === 0) {
		return resourceList;
	}
	return resourceList.filter(
		(item) => item[parameters[0]] === parameters[1]
	);
};

const APIPostRequest = async (resourceURL, content) => {
	if (content == null) {
		return false;
	}
	const resourceList = storage.get(resourceURL);
	resourceList.push(content);
	storage.set(resourceURL, resourceList);
	return true;
};

const APIPutRequest = async (resourceURL, parameters, content) => {
	if (content == null) {
		return false;
	}
	if (parameters == null || parameters.length === 0) return false;

	const resourceList = storage.get(resourceURL);
	const selectedItemIdx = resourceList.findIndex(
		(item) => item[parameters[0]] === parameters[1]
	);

	console.log(selectedItemIdx);
	if (selectedItemIdx == null) {
		return false;
	}
	resourceList[selectedItemIdx] = content;
	storage.set(resourceURL, resourceList);
	return true;
};

const APIDeleteRequest = async (resourceURL, parameters) => {
	if (parameters == null || parameters.length === 0) return false;
	const resourceList = storage.get(resourceURL);
	storage.set(
		resourceURL,
		resourceList.filter(
			(item) => item[parameters[0]] !== parameters[1]
		)
	);
	return true;
};

const APIRequest = async (
	method,
	resource,
	options = { parameters: {}, content: {} }
) => {
	if (options == null) options = { parameters: {}, content: {} };
	if (options.parameters == null) options.parameters = {};
	if (options.content == null) options.content = {};

	const parametersArr = Object.entries(options.parameters)[0];
	const resourceURL = getResourceURL(resource);
	switch (method) {
		case METHOD.POST:
			return await APIPostRequest(resourceURL, options.content);
		case METHOD.GET:
			return await APIGetRequest(resourceURL, parametersArr);
		case METHOD.PUT:
			return await APIPutRequest(
				resourceURL,
				parametersArr,
				options.content
			);
		case METHOD.DELETE:
			return await APIDeleteRequest(resourceURL, parametersArr);
		default:
			throw new Error('Invalid method');
	}
};

export default APIRequest;
