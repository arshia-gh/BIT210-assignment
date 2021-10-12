import Administrator from '../model/Administrator';
import Batch from '../model/Batch';
import HealthcareCenter from '../model/HealthcareCentre';
import Patient from '../model/Patient';
import Vaccination from '../model/Vaccination';
import Vaccine from '../model/Vaccine';

/**
 * @readonly
 * @enum {number}
 */
export const METHOD = {
	POST: 0,
	GET: 1,
	PATCH: 2
};

/**
 * @readonly
 * @enum {{URI: string, MODEL: Function}} available resources
 */
export const RESOURCE = {
	ADMINISTRATOR: {
		URI: Administrator.name,
		MODEL: Administrator
	},
	BATCH: {
		URI: Batch.name,
		MODEL: Batch
	},
	HEALTHCARE_CENTER: {
		URI: HealthcareCenter.name,
		MODEL: HealthcareCenter
	},
	PATIENT: {
		URI: Patient.name,
		MODEL: Patient
	},
	VACCINATION: {
		URI: Vaccination.name,
		MODEL: Vaccination
	},
	VACCINE: {
		URI: Vaccine.name,
		MODEL: Vaccine
	}
};

/**
 * @readonly
 * @enum {{URI: string}}
 */
export const AUTH_RESOURCE = {
	AUTHENTICATE: {
		URI: 'authenticate',
		METHOD: METHOD.GET
	},
	LOGIN: {
		URI: 'login',
		METHOD: METHOD.POST
	},
	LOGOUT: {
		URI: 'logout',
		METHOD: METHOD.GET
	}
};

/**
 * @readonly
 * @enum {number}
 */
export const ERROR_CODE = {
	BAD_REQUEST: 400,
	UNAUTHORIZED: 401,
	FORBIDDEN: 403,
	NOT_FOUND: 404,
	CONFLICT: 409
};

/**
 * @param {RESOURCE} resource
 * @return {boolean} a boolean value indicating whether the resource is valid
 */
export const isValidResource = (resource) => {
	return RESOURCE_ARRAY.some((res) => resource === res);
};

export const RESOURCE_ARRAY = Object.values(RESOURCE).map(
	(res) => res.URI
);
