import { request, RESOURCE } from '../API';
import { Modal } from 'bootstrap';
import HealthcareCenter from '../model/HealthcareCenter';
import Patient from '../model/Patient';
import User from '../model/User';
import Administrator from '../model/Administrator';
import { fillUserData } from '../main';

class AuthForm {
	constructor(redirect) {
		// find the form elements
		this.registrationForm = document.getElementById('registrationForm');
		this.loginForm = document.getElementById('loginForm');
		this.redirect = redirect;

		// find the modals
		this.registrationModal = document.getElementById('registrationModal');
		this.loginModal = document.getElementById('loginModal');

		this.registration = {
			// error alert
			alert: document.getElementById('alertRegistrationForm'),

			userType: this.registrationForm.userType,

			// user
			username: this.registrationForm.username,
			password: this.registrationForm.password,

			// healthcare
			healthcareFieldset: this.registrationForm.healthcareFieldset,
			healthcareName: this.registrationForm.healthcareName,
			healthcareAddress: this.registrationForm.healthcareAddress,
			healthcareDatalist: document.getElementById('healthcareDatalist'),

			// personal
			fullName: this.registrationForm.fullName,
			emailAddress: this.registrationForm.emailAddress,

			// admin
			staffId: this.registrationForm.staffId,

			// patient
			ICPassport: this.registrationForm.ICPassport
		};

		this.login = {
			alert: document.getElementById('alertLoginForm'),
			username: this.loginForm.username,
			password: this.loginForm.password
		};
	}

	async init() {
		return this.attachListeners().setHealthcareDatalist();
	}

	async setHealthcareDatalist() {
		const healthcareCenters = await request(RESOURCE.HEALTHCARE_CENTER);
		this.registration.healthcareDatalist.append(
			...healthcareCenters.map((hc) => {
				const optionEl = document.createElement('option');
				optionEl.value = `${hc.centerName}`;
				optionEl.textContent = `${hc.address}`;
				optionEl.dataset.id = hc.uid;
				return optionEl;
			})
		);

		return this;
	}

	getOptionFromHCDataList(healthcareName) {
		return this.registration.healthcareDatalist.querySelector(
			`option[value="${healthcareName}"]`
		);
	}

	attachListeners() {
		this.registrationForm.addEventListener('input', (event) => {
			const healthcareAddressInp = this.registration.healthcareAddress;
			const target = event.target;

			if (target.name === 'userType') {
				this.changeRegistrationType(target.value);
			}

			if (target.name === 'healthcareName') {
				const option = this.getOptionFromHCDataList(target.value);
				const hcExists = option != null; // if option is not null means hc exists
				healthcareAddressInp.value = hcExists ? option.textContent : '';
				healthcareAddressInp.disabled = hcExists;
			}
		});

		this.registrationForm.addEventListener('submit', async (event) => {
			event.preventDefault();
			if (!this.registrationForm.checkValidity()) return;

			// user type
			const userType = this.registration.userType.value;

			// healthcare info
			const hcName = this.registration.healthcareName.value;
			const hcAddress = this.registration.healthcareAddress.value;

			// user info
			const username = this.registration.username.value;
			const password = this.registration.password.value;

			// personal info
			const fullName = this.registration.fullName.value;
			const email = this.registration.emailAddress.value;
			const staffID = this.registration.staffId.value;
			const ICPassport = this.registration.ICPassport.value;

			let hcObject = await request(RESOURCE.HEALTHCARE_CENTER, {
				query: { centerName: hcName }
			})?.at(0);

			let dashboardURL = 'patient';
			let newUser;

			try {
				if (userType === 'administrator') {
					if (hcObject.length === 0) {
						hcObject = await HealthcareCenter.create(hcName, hcAddress);
					} else hcObject = hcObject[0];

					newUser = await hcObject.createAdministrator(
						username,
						password,
						email,
						fullName,
						staffID
					);
					dashboardURL = 'administrator';
				} else {
					newUser = await Patient.create(username, password, email, fullName, ICPassport);
				}
				this.setSuccess(
					this.login.alert,
					`Success - account was created for ${newUser.fullName}`
				);
				Modal.getOrCreateInstance(this.registrationModal).hide();
				Modal.getOrCreateInstance(this.loginModal).show();
			} catch (error) {
				this.setError(this.registration.alert, error.message);
			}
		});

		this.loginForm.addEventListener('submit', async (event) => {
			event.preventDefault();
			if (!this.loginForm.checkValidity()) return;

			const username = this.login.username.value;
			const password = this.login.password.value;

			let dashboardURL = 'patient.html';
			try {
				const user = await User.login(username, password);
				if (user instanceof Administrator) {
					dashboardURL = 'administrator.html';
				}
				if (this.redirect) {
					window.location.replace(`/dashboard/${dashboardURL}`);
				} else {
					Modal.getOrCreateInstance(this.loginModal).hide();
					fillUserData();
				}
			} catch (error) {
				this.loginForm.reset();
				this.setError(this.login.alert, error.message);
			}
		});

		// set the user type when registration modal is shown
		this.registrationModal.addEventListener('show.bs.modal', (event) => {
			const relatedTarget = event.relatedTarget;
			if (relatedTarget) {
				const userType = relatedTarget.dataset.registrationType;
				this.changeRegistrationType(userType || 'patient');
			}
		});

		this.registrationForm.addEventListener('reset', (event) => {
			event.preventDefault();
			this.hideAlert(this.registration.alert);
			const currentType = this.registration.userType.value;
			this.registrationForm.reset();
			this.registration.alert.classList.add('d-none');
			this.registrationForm.classList.remove('was-validated');
			this.changeRegistrationType(currentType);
		});

		this.loginForm.addEventListener('reset', () => {
			this.hideAlert(this.login.alert);
			this.loginForm.classList.remove('was-validated');
		});

		this.registrationModal.addEventListener('hide.bs.modal', () => {
			this.hideAlert(this.registration.alert);
			this.registrationForm.reset();
		});

		this.loginModal.addEventListener('hide.bs.modal', () => {
			this.hideAlert(this.login.alert);
			this.loginForm.reset();
		});

		return this;
	}

	/**
	 * Show a success alert with a custom message
	 * @param {HTMLElement} alertEl
	 * @param {string} msg
	 */
	setSuccess(alertEl, msg) {
		this.hideAlert(alertEl);
		alertEl.textContent = msg;
		alertEl.classList.remove('d-none');
		alertEl.classList.add('alert-success');
	}

	/**
	 * Show a danger alert with a custom message
	 * @param {HTMLElement} alertEl
	 * @param {string} msg
	 */
	setError(alertEl, msg) {
		this.hideAlert(alertEl);
		alertEl.textContent = msg;
		alertEl.classList.remove('d-none');
		alertEl.classList.add('alert-danger');
	}

	/**
	 * hide the alert and reset the stylings
	 * @param {HTMLElement} alertEl
	 */
	hideAlert(alertEl) {
		alertEl.textContent = '';
		alertEl.classList.add('d-none');
		alertEl.classList.remove('alert-success');
		alertEl.classList.remove('alert-danger');
	}

	/**
	 * changes the type of registration form
	 * @param {'administrator' | 'patient'} type target type
	 */
	changeRegistrationType(type) {
		// change the userType radio button value
		this.registration.userType.value = type;
		// get fieldset and form control elements
		const healthcareFS = this.registration.healthcareFieldset;
		const staffIdFC = this.registration.staffId.parentElement;
		const staffIdInp = this.registration.staffId;
		const ICPassportFC = this.registration.ICPassport.parentElement;
		const ICPassportInp = this.registration.ICPassport;

		const isAdminister = type === 'administrator';
		healthcareFS.classList[isAdminister ? 'remove' : 'add']('d-none');
		Array.from(healthcareFS.querySelectorAll('input')).forEach(
			(inp) => (inp.required = isAdminister)
		);
		staffIdFC.classList[isAdminister ? 'remove' : 'add']('d-none');
		staffIdInp.required = isAdminister;
		ICPassportFC.classList[!isAdminister ? 'remove' : 'add']('d-none');
		ICPassportInp.required = !isAdminister;
	}
}

export default AuthForm;
