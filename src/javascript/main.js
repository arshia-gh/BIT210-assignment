import bootstrap from 'bootstrap';
import { seed, storage } from './API';
import User from './model/User';

export default async () => {
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

export const fillUserData = async (user) => {
	const ul = document.getElementById('userInfo'); //the <ul>
	const specialInfo = user.constructor.name === 'Patient' ? 'ICPassport' : 'staffID';
	ul.innerHTML = ''; //remove all child

	for (const info of ['fullName', specialInfo, 'email']) {
		const li = document.createElement('li');
		li.className = 'list-group-item';
		li.innerHTML = user[info];
		ul.append(li);
	}
};

export const toggleLogout = (show) => {
	const logoutDiv = document.getElementById('logout');
	logoutDiv.classList[show ? 'remove' : 'add']('d-hide');
};

export const toggleLoginRegister = () => {
	const loginRegister = document.getElementById('loginRegister');
	loginRegister.classList[show ? 'remove' : 'add']('d-hide');
};

export const attachLogoutListener = (url) => {
	const logoutBtn = document.getElementById('logoutBtn');
	if (logoutBtn != null) {
		logoutBtn.addEventListener('click', async () => {
			await User.logout();
			window.location.replace(url);
		});
	}
};
