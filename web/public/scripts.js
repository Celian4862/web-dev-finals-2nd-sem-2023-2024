function ForceSubmitForm(form, button = undefined, confirmation = false) {
	if (!(form instanceof HTMLFormElement)) {
		return;
	}

	if (confirmation && !confirm("Are you sure you want to continue?")) {
		return;
	}

	if (button instanceof HTMLButtonElement) {
		const hiddenInput = document.createElement("input");

		hiddenInput.type = "hidden";
		hiddenInput.name = button.name;
		hiddenInput.value = button.value;

		form.appendChild(hiddenInput);
	}

	Array.from(form.elements).forEach((node) => {
		if (
			node instanceof HTMLInputElement ||
			node instanceof HTMLSelectElement ||
			node instanceof HTMLTextAreaElement
		) {
			node.removeAttribute("required");
		}
	});

	form.submit();
}
