function ForceSubmitForm(form, button = undefined) {
	if (!(form instanceof HTMLFormElement)) {
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
