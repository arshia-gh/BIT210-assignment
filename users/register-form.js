import {initForm, isString} from "./form-validator.js";

// RFC5322 email regexp
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

	// initialize the form
	const form = initForm('registrationForm', {
		username: isString().required().minLen(3).maxLen(25).pattern(/^[a-zA-Z_]*$/),
		password: isString().required().minLen(12).pattern(/^(?=.*[A-Z]+).*$/),
		healthcareAddress: isString('centre address', isAdmin).required(),
		healthcareName: isString('centre name', isAdmin).required(),
		fullName: isString('name').required().minLen(3).pattern(/^[a-zA-Z\s]*$/),
		ICPassport: isString('IC/Passport', isPatient).required(),
		emailAddress: isString('email address').required().pattern(email_regexp),
		staffID: isString('staff ID', isAdmin).required()
	});

	// validate inputs on form submission
	form.element.addEventListener('submit', function (event) {
		event.preventDefault();
		if (form.checkValidity()) {
			this.submit();
		}
	})

	form.element.addEventListener('input', function ({target}) {
		// change the user type if user clicks on the userType radio button
		if (target.name === 'userType') {
			changeUserType(target.value);
		// set the healthcare centre address when user selects from the healthcare centre datalist
		} else if (target.name === 'healthcareName') {
			setHealthcareAddress(form.inputs.healthcareName.target, form.inputs.healthcareAddress.target);
		}
	})

	// set selected healthcare centre address
	const setHealthcareAddress = (centreNameInp, centreAddressInp) => {
		// get the selected option
		const selectedOption = centreNameInp.list.querySelector(`option[value='${centreNameInp.value}']`);
		// set the address value
		centreAddressInp.value = selectedOption != null ? selectedOption.textContent : '';
		centreAddressInp.readOnly = !!selectedOption;
	}

	// changes the user type of the form
	const changeUserType = (userType) => {
		// check if its admin (null on invalid user type)
		const isAdmin = userType === 'administrator'
			? true : userType === 'patient'
				? false : null

		// return if the user type is invalid
		if (isAdmin === null) return;

		// change visibility of inputs that depend on the userType

		// admin fields
		administratorType.checked = isAdmin;
		healthcareFieldset.hidden = !isAdmin;
		form.inputs.staffID.target.parentElement.hidden = !isAdmin;

		// patient fields
		patientType.checked = !isAdmin;
		form.inputs.ICPassport.target.parentElement.hidden = isAdmin;

		// reset the validation styles
		form.resetValidation();
	}

	// change the user type on page initialization
	changeUserType(getCookie('userType') || 'patient');
	// set the healthcare centre address as it might have been set by php
	setHealthcareAddress(form.inputs.healthcareName.target, form.inputs.healthcareAddress.target)
}

/**
 * A function used to retrieve the value of a cookie
 * @param cookie_name
 * @returns {string} found cookie value
 */
const getCookie = (cookie_name) => {
	let name = cookie_name + "=";
	let decodedCookie = decodeURIComponent(document.cookie);
	let cookies = decodedCookie.split(';');
	for (const cookie of cookies) {
		const formatted_cookie = cookie.trimStart();
		if (formatted_cookie.indexOf(name) === 0) {
			return formatted_cookie.slice(name.length);
		}
	}
	return "";
}