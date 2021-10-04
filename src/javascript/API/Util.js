import { APIConfig } from '../config';

export const METHOD = {
	POST: 0,
	GET: 1,
	PUT: 2,
	DELETE: 3
};

export const RESOURCE = {
	PATIENTS: 0,
	VACCINATIONS: 1,
	VACCINES: 2,
	BATCHES: 3
};

export const getResourceURL = (resource) => {
	let selected;
	switch (resource) {
		case RESOURCE.PATIENTS:
			selected = 'patients';
			break;
		case RESOURCE.VACCINATIONS:
			selected = 'vaccinations';
			break;
		case RESOURCE.VACCINES:
			selected = 'vaccines';
			break;
		case RESOURCE.BATCHES:
			selected = 'batches';
			break;
		default:
			throw new Error('Invalid resource');
	}
	return `${APIConfig.API_URL}/${selected}`;
};

function jsonParser(blob) {
	let parsed = JSON.parse(blob);
	if (typeof parsed === 'string') parsed = jsonParser(parsed);
	return parsed;
}

export const getResource = (resourceURL) => {
	return jsonParser(localStorage.getItem(resourceURL));
};

export const setResource = (resourceURL, value) => {
	localStorage.setItem(resourceURL, JSON.stringify(value));
	return true;
};

export default {
	METHOD,
	RESOURCE,
	getResourceURL,
	getResource,
	setResource
};
