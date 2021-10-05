import { APIConfig } from 'javascript/config';

/* [TODO]
	[-] Modularize RESOURCE AND METHOD for better maintainability
*/

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
	return `${APIConfig.URL}/${selected}`;
};
