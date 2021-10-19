import ApiError from './ApiError';
import { ERROR_CODE, RESOURCE_ARRAY, RESOURCE } from './utils';

/**
 *@typedef {Array} rows
 */

const SESSION_URI = 'session';

/**
 * filter rows of data using query strings
 * @param {rows} rows
 * @returns {rows} - filtered row(s)
 */
export const filterRows = (rows, query) => {
	if (!Array.isArray(rows)) return null;
	if (query == null) query = {};
	const queryKeys = Object.keys(query);
	return rows.filter((row) => {
		return queryKeys.every((key) => {
			return row[key] === query[key];
		});
	});
};

/**
 * find the first row index that matches the query string
 * @param {rows} rows
 * @returns {number} if the row is found it returns the row index otherwise it returns (-1)
 */
export const findRowIndex = (rows, query) => {
	if (!Array.isArray(rows) || query == null) return -1;
	const queryKeys = Object.keys(query);
	return rows.findIndex((row) => {
		return queryKeys.every((key) => {
			return row[key] === query[key];
		});
	});
};



const extendedLocalStorage = {
	/**
	 * to retrieve a resource
	 * @param {string} resource - resource URI
	 * @returns {*} specified resource
	 */
	get(resource) {
		return JSON.parse(localStorage.getItem(resource));
	},

	/**
	 * to retrieve a resource
	 * @param {string} resource - resource URI
	 * @param {*} value - the value to write
	 */
	set(resource, value) {
		localStorage.setItem(resource, JSON.stringify(value));
	},

	/**
	 * @param {string} resource - resource URI
	 * @param {Object.<string, string | number>} query - query string (ex: /test?id=10)
	 */
	read(resource, query) {
		if (query == null) query = {};

		/** @type {rows} */
		const retrievedRows = this.get(resource);
		const filteredRows = filterRows(retrievedRows, query);
		return filteredRows;
	},

	/**
	 * @param {string} resource - recourse URI
	 * @param {*} data - data to be added to the collection
	 */
	create(resource, data) {
		if (data == null)
			throw new ApiError(
				'Data must be defined',
				ERROR_CODE.BAD_REQUEST
			);
		if (data.uid == null)
			throw new ApiError(
				'UID Must be defined',
				ERROR_CODE.BAD_REQUEST
			);

		/** @type {rows} */
		const retrievedRows = this.get(resource);

		if (findRowIndex(retrievedRows, { uid: data.uid }) !== -1) {
			throw new ApiError(
				`An entry with the same uid exist`,
				ERROR_CODE.CONFLICT
			);
		}

		retrievedRows.push(data);
		this.set(resource, retrievedRows);

		return this.read(resource, { uid: data.uid })[0];
	},

	/**
	 * @param {string} resource - recourse URI
	 * @param {*} data - new data
	 * @param {Object.<string, string | number>} query - query string (ex: /test?id=10)
	 */
	update(resource, data, query) {
		if (data == null) throw new Error('Data must be defined');
		if (data.uid == null) throw new Error('UID Must be defined');

		const retrievedRows = this.get(resource);
		const toUpdateIndex = findRowIndex(retrievedRows, query);
		if (toUpdateIndex === -1) {
			throw new ApiError(
				`Entry with requested uid was not found`,
				ERROR_CODE.NOT_FOUND
			);
		}
		let toUpdateRow = retrievedRows[toUpdateIndex];
		toUpdateRow = { ...toUpdateRow, ...data };
		retrievedRows[toUpdateIndex] = toUpdateRow;

		this.set(resource, retrievedRows);
		return this.read(resource, { uid: toUpdateRow.uid })[0];
	},

	authenticate() {
		const retrievedSession = this.get(SESSION_URI);
		if (retrievedSession == null) {
			throw new ApiError('not authorized', ERROR_CODE.UNAUTHORIZED);
		}
		return retrievedSession.type === RESOURCE.ADMINISTRATOR.MODEL.name
			? RESOURCE.ADMINISTRATOR.MODEL.fromParsedJson(
					retrievedSession.user
			  )
			: RESOURCE.PATIENT.MODEL.fromParsedJson(retrievedSession.user);
	},

	login(username, password) {
		const retrievedSession = this.get(SESSION_URI);
		for (const resource of [
			RESOURCE.ADMINISTRATOR,
			RESOURCE.PATIENT
		]) {
			const retrievedData = this.get(resource.URI);
			const foundUser = retrievedData.find((user) => {
				return (
					user.username === username && user.password === password
				);
			});
			if (foundUser != null) {
				this.set(SESSION_URI, {
					user: foundUser,
					type: resource.MODEL.name
				});
				return resource.MODEL.fromParsedJson(foundUser);
			}
		}
		throw new ApiError(
			'Given credentials did not match any registered user',
			ERROR_CODE.UNAUTHORIZED
		);
	},

	logout() {
		const retrievedData = this.get(SESSION_URI);
		if (retrievedData == null) {
			throw new ApiError(
				'You must be logged in before logging-out',
				ERROR_CODE.UNAUTHORIZED
			);
		}
		this.set(SESSION_URI, null);
	},

	clear() {
		localStorage.clear();
		RESOURCE_ARRAY.forEach((resource) => {
			this.set(resource, []);
		});
	},

	isEmpty() {
		return localStorage.length === 0;
	}
};

export default extendedLocalStorage;
