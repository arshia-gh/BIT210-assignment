import {
	METHOD,
	getResourceURL,
	getResource,
	setResource
} from './Util';

const APIGetRequest = async (resourceURL, parameter) => {
	const resourceList = getResource(resourceURL);
	if (parameter == null) {
		return resourceList;
	}
	return resourceList[parameter];
};

const APIPostRequest = async (resourceURL, content) => {
	if (content == null) {
		throw new Error('Invalid "POST" content');
	}
	const resourceList = getResource(resourceURL);
	resourceList.push(content);
	setResource(resourceURL, resourceList);
};

const APIPutRequest = async (resourceURL, parameter, content) => {
	if (content == null) {
		throw new Error('Invalid "PUT" content');
	}
	const resourceList = getRecourseList(resourceURL);
	const selectedItemIdx = resourceList.find(
		(item) => item.id === parameter
	);
	if (selectedItemIdx == null) {
		throw new Error('Invalid "PUT" parameter');
	}
	resourceList[selectedItemIdx] = content;
	setResource(resourceURL, resourceList);
};

const APIDeleteRequest = async (resourceURL, parameter) => {
	if (parameter == null) {
		throw new Error('Invalid "DELETE" parameter');
	}
	const resourceList = getResource(resourceURL);
	setResource(
		resourceURL,
		resourceList.filter((item) => item.id !== parameter)
	);
};

const APIRequest = async (
	method,
	resource,
	options = { parameter: undefined, content: undefined }
) => {
	const resourceURL = getResourceURL(resource);
	switch (method) {
		case METHOD.POST:
			return await APIPostRequest(resourceURL, options.content);
		case METHOD.GET:
			return await APIGetRequest(resourceURL, options.parameter);
		case METHOD.PUT:
			return await APIPutRequest(
				resourceURL,
				options.parameter,
				options.content
			);
		case METHOD.DELETE:
			return await APIDeleteRequest(resourceURL, options.parameter);
		default:
			throw new Error('Invalid method');
	}
};

export default APIRequest;
