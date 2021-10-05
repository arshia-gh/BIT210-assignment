import { RESOURCE, getResourceURL } from './utils';
import { APIConfig } from 'javascript/config';
import Vaccine from 'javascript/model/Vaccine';
import Vaccination from 'javascript/model/Vaccination';
import Batch from 'javascript/model/Batch';

const vaccines = [
	new Vaccine(1, 'Pfizer', 'Pfizer Biotech Ltd'),
	new Vaccine(2, 'Sinovac', 'Sinovac Biotech Ltd'),
	new Vaccine(3, 'AstraZeneca', 'AstraZeneca Biotech Ltd')
];

const batches = [
	new Batch('PF01', '2021-03-15', 100, vaccines[0], 'ABC healthcare'),
	new Batch('SI01', '2021-04-20', 200, vaccines[1], 'ABC healthcare'),
	new Batch('PF02', '2021-01-12', 100, vaccines[0], 'Good healthcare')
];

const vaccinations = [
	new Vaccination('v001', '2021-04-20', batches[0]),
	new Vaccination('v002', '2021-01-20', batches[1]),
	new Vaccination('v003', '2021-02-20', batches[0])
];

const extendedLocalStorage = {
	get(resourceURL) {
		return JSON.parse(localStorage.getItem(resourceURL));
	},

	set(resourceURL, value) {
		return localStorage.setItem(resourceURL, JSON.stringify(value));
	},

	init() {
		localStorage.clear();
		for (const resource in RESOURCE) {
			if (RESOURCE.hasOwnProperty(resource)) {
				this.set(`${APIConfig.URL}/${resource.toLowerCase()}`, []);
			}
		}
	},

	seed() {
		this.init();
		this.set(getResourceURL(RESOURCE.VACCINES), vaccines);
		this.set(getResourceURL(RESOURCE.VACCINATIONS), vaccinations);
		this.set(getResourceURL(RESOURCE.BATCHES), batches);
	},

	isEmpty() {
		return localStorage.length === 0;
	}
};

export default extendedLocalStorage;
