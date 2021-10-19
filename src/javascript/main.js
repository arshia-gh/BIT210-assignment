import {} from 'bootstrap';
import { seed, storage } from './API';
import User from './model/User';

export const common = async () => {
	if (storage.isEmpty()) {
		storage.clear();
		await seed();
	}

	(function () {
		'use strict';

		// Fetch all the forms we want to apply custom Bootstrap validation styles to
		var forms = document.querySelectorAll('.needs-validation');

		// Loop over them and prevent submission
		Array.prototype.slice.call(forms).forEach(function (form) {
			form.addEventListener(
				'submit',
				function (event) {
					if (!form.checkValidity()) {
						event.preventDefault();
						event.stopPropagation();
					}

					form.classList.add('was-validated');
				},
				false
			);
		});
	})();
};

export const fillUserData = async () => {
	const logoutDiv = document.getElementById('logout');
	const loginRegisterDiv = document.getElementById('loginRegister');
	try {
		const user = await User.authenticate();
		const ul = document.getElementById('userInfo'); //the <ul>
		const specialInfo = user.constructor.name === 'Patient' ? 'ICPassport' : 'staffID';
		ul.innerHTML = ''; //remove all child

		for (const info of ['fullName', specialInfo, 'email']) {
			const li = document.createElement('li');
			li.className = 'list-group-item';
			li.innerHTML = user[info];
			ul.append(li);
		}

		loginRegisterDiv.classList.add('d-none');
		logoutDiv.classList.remove('d-none');
		return true;
	} catch (ignore) {
		loginRegisterDiv.classList.remove('d-none');
		logoutDiv.classList.add('d-none');
		return false;
	}
};
