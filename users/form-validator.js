/**
 * Pipeline implementation used in form validator
 */
class Pipeline {
	/**
	 * @param middleware middleware to be added at the point of construction
	 */
	constructor(...middleware) {
		this.stack = middleware;
	}

	/**
	 * pushes a or many middleware to the pipeline stack
	 * @param middleware middleware to be added to the stack
	 */
	push(...middleware) {
		this.stack.push(...middleware);
	}

	/**
	 * executes all middleware from the stack
	 * @param context context that will be provided to middleware
	 */
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

/**
 * A schema constructor used for form validation
 * @param name {string|null} used to conditionally run the validators
 * @param cond {function} used to construct meaningful error messages
 * @returns {{name: null, minLen(*): this, pattern(RegExp): this, required(): this, validate: *, maxLen(*): this}|*}
 */
export function isString(name = null, cond = null) {
	const pipeline = new Pipeline((ctx, next) => {
		if (cond != null && !cond()) return;
		ctx.value = String(ctx.value);
		return next();
	});
	return {
		name,
		validate: pipeline.execute.bind(pipeline),
		/**
		 * to specify the maximum character length of the schema
		 * @param limit
		 * @returns {*}
		 */
		maxLen(limit) {
			delete this.maxLen;
			pipeline.push((ctx, next) => {
				if (ctx.value.length <= limit) return next();
				throw new RangeError(`${ctx.name} must be at most ${limit} character(s)!`);
			});
			return this;
		},
		/**
		 * to specify the minimum character length of the schema
		 * @param limit
		 * @returns {*}
		 */
		minLen(limit) {
			delete this.minLen;
			pipeline.push((ctx, next) => {
				if (ctx.value.length >= limit) return next();
				throw new RangeError(`${ctx.name} must be at least ${limit} character(s)!`);
			});
			return this;
		},
		/**
		 * to specify whether the value is required
		 * @returns {*}
		 */
		required() {
			delete this.required;
			pipeline.push((ctx, next) => {
				if (ctx.value.length !== 0) return next();
				throw new Error(`${ctx.name} is required!`);
			});
			return this;
		},
		/**
		 * to specify a custom RegExp pattern that the schema should follow
		 * @param pattern {RegExp}
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

/**
 * Finds and extend inputs of a schema based on a given form element
 * @param form used to retrieve the inputs
 * @param schema used for inputs validation
 * @returns {*} extended form inputs
 */
function getInputs(form, schema) {
	return Object.keys(schema).reduce((object, inputName) => {
		/** @type {HTMLInputElement} */
		const inputElement = form.elements.namedItem(inputName);

		if (inputElement == null) throw new TypeError(`input element with the name of ${inputName} was not found`);

		object[inputName] = {
			name: schema[inputName].name ?? inputName,
			target: inputElement,
			feedbackElement: findOrCreateFeedback(inputElement),
			// add a validate function (from the schema)
			validate() {
				schema[inputName].validate({ value: this.target.value, name: this.name });
			},
		};
		return object;
	}, {});
}

/** find or create a feed back element for the given input
 * @param element {HTMLElement}
 * @returns {HTMLElement}
 */
function findOrCreateFeedback(element) {
	let feedbackEl = document.querySelector(`input[name=${element.name}] + .invalid-feedback`);
	if (feedbackEl) return feedbackEl;
	feedbackEl = document.createElement('span');
	feedbackEl.classList.add('invalid-feedback');
	element.insertAdjacentElement('afterend', feedbackEl);
	return feedbackEl;
}

/**
 * initialize the form with a given schema
 * @param {string} formId - form id
 * @param {*} schema - validation schema
 * @return {*} - extended form object
 */
export function initForm(formId, schema) {
	/** @type {HTMLFormElement} */
	const form = document.getElementById(formId);

	if (form == null) throw new TypeError(`form element with the id of ${formId} was not found`);

	// get elements name from the schema
	const elementsName = Object.keys(schema);
	// retrieve form inputs
	const inputs = getInputs(form, schema);

	form.addEventListener('input', ({ target }) => {
		if (target == null || !elementsName.includes(target.name)) return;
		inputs[target.name].target.value = target.value;
	});

	return {
		element: form,
		inputs: inputs,

		checkValidity() {
			let isFormValid = true;
			for (const inputName in this.inputs) {
				const input = this.inputs[inputName];
				try {
					this.resetStatusFor(input.target);
					input.validate();
					this.setSuccessFor(input.target);
				} catch (e) {
					isFormValid = false;
					this.setErrorFor(input.target, input.feedbackElement, e.message);
				}
			}
			return isFormValid;
		},

		resetValidation() {
			for (const inputName in this.inputs) {
				const input = this.inputs[inputName];
				this.resetStatusFor(input.target)
			}
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