import bootstrap from 'bootstrap';
import config from './config';
import { seed, storage } from './API';

export default async () => {
	if (storage.isEmpty() || config.mode === 'development') {
		storage.clear();
		await seed();
	}
};

export const fillUserData = (user) => {
	const ul = document.getElementById('userinfo'); //the <ul>
	const specialInfo = user.constructor.name === 'Patient' ? 'ICPassport' : 'staffID';
	ul.innerHTML = ''; //remove all child

	for (const info of ['fullName', specialInfo, 'email']) {
		const li = document.createElement('li');
		li.className = "list-group-item";
		li.innerHTML = user[info];
		ul.append(li);
	}
}
