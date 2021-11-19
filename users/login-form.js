import {initForm, isString} from './form-validator.js'

document.addEventListener('DOMContentLoaded', () => {
	init();
})

const init = () => {
	const form = initForm('loginForm', {
		username: isString().required().minLen(3).maxLen(25),
		password: isString().required().minLen(12)
	});

	form.element.addEventListener('submit', function (event) {
		event.preventDefault();
		if (form.checkValidity()) {
			this.submit();
		}
	})
}