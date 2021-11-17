document.addEventListener('DOMContentLoaded', () => {
	init();
})

/**
 * Initializes the application through performing certain function invocations
 */
const init = () => {
	enableToolTips();
	const form = getDocumentForm();
	// silently end the script if form is not found (FATAL_ERR)
	if (form == null) return;

	// find the submit btn
	const submitButton = form.elements['submitBtn'];

	// silently end the script if submit button is not found (FATAL_ERR)
	if (submitButton == null) return;
	if (submitButton.nodeName !== 'BUTTON') return;

	addListItemListener((isSelected) => {
		disableElement(submitButton, !isSelected);
	});
}

/**
 * Enables all bootstrap tooltips of the document
 */
const enableToolTips = () => {
	// enable all tooltips
	const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
	tooltipTriggerList.forEach((tooltipTriggerEl) => {
		new bootstrap.Tooltip(tooltipTriggerEl)
	});
}

/**
 * Gets the form from document <br />
 * - All documents excluding index.php contain a form. <br />
 * - to apply DRY principle this js works with all documents under patient/ dir <br />
 * @return {HTMLFormElement|null} returns the found form element
 */
const getDocumentForm = () => {
	const idToSearch = 'documentForm';
	const foundElement = document.getElementById(idToSearch);
	if (foundElement && foundElement.nodeName === 'FORM') {
		return foundElement;
	}
	return null;
}

/**
 * disables the given element if the condition is true
 * @param element {HTMLElement}
 * @param cond {boolean}
 */
const disableElement = (element, cond) => {
	if (element == null) return null;
	if (!element instanceof HTMLElement) return null;
	if (typeof cond !== 'boolean') return null;

	element.disabled = cond;
	element.classList[cond ? 'add' : 'remove']('disabled');
}

/**
 * Add event listener to list item that have a child input <br>
 * triggers the given callback if li is clicked <br>
 * and set the checked status of the child input to TRUE
 * @param callback {function}
 */
const addListItemListener = (callback) => {
	const listItems = document.querySelectorAll("li");
	Array.from(listItems).forEach(li => {
		const input = li.querySelector('input[type=radio]');
		if (input != null) {
			li.addEventListener('click', () => {
				input.checked = true;
				callback != null && callback(input.checked)
			});
		}
	});
}