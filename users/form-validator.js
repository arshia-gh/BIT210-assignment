class Pipeline {
	constructor(...middleware) {
		this.stack = middleware;
	}

	push(...middleware) {
		this.stack.push(...middleware);
	}

	execute(context) {
		let prevIndex = -1;
		const runner = (index) => {
			if (index === prevIndex) {
				throw new Error('next() is called more than once');
			}

			prevIndex = index;
			const middleware = this.stack[index];

			if (middleware) {
				middleware(context, () => {
					runner(index + 1);
				});
			}
		};
		runner(0);
	}
}

export function isString(cond = null, name = null) {
	const pipeline = new Pipeline((ctx, next) => {
		if (cond != null && !cond()) return;
		ctx.value = String(ctx.value);
		return next();
	});
	return {
		name,
		validate: pipeline.execute.bind(pipeline),
		maxLen(limit) {
			delete this.maxLen;
			pipeline.push((ctx, next) => {
				if (ctx.value.length <= limit) return next();
				throw new RangeError(`${ctx.name} must be at most ${limit} character(s)!`);
			});
			return this;
		},
		minLen(limit) {
			delete this.minLen;
			pipeline.push((ctx, next) => {
				if (ctx.value.length >= limit) return next();
				throw new RangeError(`${ctx.name} must be at least ${limit} character(s)!`);
			});
			return this;
		},
		required() {
			delete this.required;
			pipeline.push((ctx, next) => {
				if (ctx.value.length !== 0) return next();
				throw new Error(`${ctx.name} is required!`);
			});
			return this;
		},
		/**
		 * @param {RegExp} pattern
		 */
		pattern(pattern) {
			delete this.pattern;
			pipeline.push((ctx, next) => {
				if (pattern.test(ctx.value)) return next();
				throw new Error(`${ctx.name} is not valid!`);
			});
			return this;
		}
	};
}

function getBoundInputs(form, schema) {
	return Object.keys(schema).reduce((object, inputName) => {
		/** @type {HTMLInputElement} */
		const inputElement = form.elements.namedItem(inputName);

		if (inputElement == null) throw new TypeError(`input element with the name of ${inputName} was not found`);

		object[inputName] = new Proxy(
			{
				element: inputElement,
				value: inputElement.value,
				feedbackElement: findOrCreateFeedback(inputElement),

				validate() {
					schema[inputName].validate({value: this.value, name: schema[inputName].name ?? inputName});
				},
			}, {
				set(target, property, value) {
					if (property !== 'value' && property !== 'hidden' && property !== 'readOnly') return false;
					if (property === 'value') {
						target[property] = value;
						target['element'].value = value;
					} else if (property === 'hidden') {
						target['element'].parentElement.hidden = value;
					} else {
						target['element'].readOnly = value;
					}
					return true;
				}
			});
		return object;
	}, {});
}

function findOrCreateFeedback(element) {
	let feedbackEl = document.querySelector(`input[name=${element.name}] + .invalid-feedback`);
	if (feedbackEl) return feedbackEl;
	feedbackEl = document.createElement('span');
	feedbackEl.classList.add('invalid-feedback');
	element.insertAdjacentElement('afterend', feedbackEl);
	return feedbackEl;
}

/**
 *
 * @param {string} formId - form id
 * @param {*} schema - validation schema
 * @return {any} - extended form object
 */
export function initForm(formId, schema) {
	/** @type {HTMLFormElement} */
	const form = document.getElementById(formId);

	if (form == null) throw new TypeError(`form element with the id of ${formId} was not found`);

	const elementsName = Object.keys(schema);
	const boundInputs = getBoundInputs(form, schema);

	form.addEventListener('input', ({target}) => {
		if (target == null || !elementsName.includes(target.name)) return;
		boundInputs[target.name].value = target.value;
	});

	return {
		element: form,
		inputs: boundInputs,

		checkValidity() {
			let isFormValid = true;
			for (const inputName in this.inputs) {
				const input = this.inputs[inputName];
				try {
					this.resetStatusFor(input.element);
					input.validate();
					this.setSuccessFor(input.element);
				} catch (e) {
					isFormValid = false;
					this.setErrorFor(input.element, input.feedbackElement, e.message);
				}
			}
			return isFormValid
		},

		setErrorFor(element, feedbackEl, msg) {
			element.classList.add('is-invalid');
			feedbackEl.textContent = msg;
		},

		setSuccessFor(element) {
			element.classList.add('is-valid');
		},

		resetStatusFor(element) {
			element.classList.remove('is-invalid');
			element.classList.remove('is-valid');
		},

		getElementByName(name) {
			return this.element.querySelector(`input[name=${name}]`);
		}
	};
}