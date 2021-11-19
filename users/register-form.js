import {initForm, isString} from "./form-validator.js";

const email_regexp = /(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9]))\.){3}(?:(2(5[0-5]|[0-4][0-9])|1[0-9][0-9]|[1-9]?[0-9])|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])/;

document.addEventListener('DOMContentLoaded', () => {
	init();
})

const init = () => {
	const healthcareFieldset = document.getElementById('healthcareFieldset');
	const administratorType = document.getElementById('administratorUserType');
	const patientType = document.getElementById('patientUserType');

	const isAdmin = () => administratorType.checked
	const isPatient = () => patientType.checked

	const form = initForm('registrationForm', {
		username: isString().required().minLen(3).maxLen(25),
		password: isString().required().minLen(12),
		healthcareAddress: isString(isAdmin, 'centre address').required(),
		healthcareName: isString(isAdmin, 'centre name').required(),
		fullName: isString(null, 'name').required().minLen(3).pattern(/[a-zA-Z\s]./),
		ICPassport: isString(isPatient, 'IC/Passport').required(),
		emailAddress: isString(null, 'email address').required().pattern(email_regexp),
		staffID: isString(isAdmin, 'staff ID').required()
	});

	form.element.addEventListener('submit', function (event) {
		event.preventDefault();
		if (form.checkValidity()) {
			this.submit();
		}
	})

	form.element.addEventListener('change', function ({target}) {
		if (target.name === 'userType') {
			changeUserType(target.value === 'administrator');
		}
	})

	const changeUserType = (isAdmin) => {
		// get fieldset and form control elements
		administratorType.checked = isAdmin;
		healthcareFieldset.hidden = !isAdmin;
		form.inputs.staffID.hidden = !isAdmin;

		patientType.checked = !isAdmin;
		form.inputs.ICPassport.hidden = isAdmin;
	}

	changeUserType(false);
}

