import { RESOURCE } from '.';
import { APIConfig } from '../config';

const resetLocalStorage = () => {
	localStorage.clear();
	for (const resource in RESOURCE) {
		if (RESOURCE.hasOwnProperty(resource)) {
			localStorage.setItem(
				`${APIConfig.API_URL}/${resource.toLowerCase()}`,
				JSON.stringify([])
			);
		}
	}
};

export default resetLocalStorage;
